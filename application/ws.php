<?php

// Set no time limit
set_time_limit(0);

// Ignore user about
ignore_user_abort(1);

// Open pid-file
$pid = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'ws.pid', 'c');

// Try to lock pid-file
$flock = flock($pid, LOCK_SH | LOCK_EX | LOCK_NB, $wouldblock);

// If opening of pid-file failed, or locking failed and locking wouldn't be blocking - thow exception
if ($pid === false || (!$flock && !$wouldblock)) 
    throw new Exception('Error opening or locking lock file, may be caused by pid-file permission restrictions');

// Else if pid-file was already locked - exit
else if (!$flock && $wouldblock) 
    exit('Another instance is already running; terminating.');

// Erase pid-file and write current pid into it
ftruncate($pid, 0); fwrite($pid, getmypid());

/**
 * Shutdown function, for use as a shutdown handler
 */
function shutdown() {

    // Erase pid-file
    ftruncate($pid, 0);
    
    // Release the lock
    flock($pid, LOCK_UN);
    
    // Close server stream
    fclose($server);
}

// Register shutdown handler function
register_shutdown_function('shutdown');

// Create socket server
$server = stream_socket_server('tcp://0.0.0.0:8888/', $errno, $errstr);

// If socket server creation failed - exit
if (!$server) die('Can\'t start socket server: $errstr ($errno)');

// Clients' streams array
$clientA = array();

// Meta array, containing info about what users the active streams belongs to, and what roles those users are
$channelA = array();

// Start serving
while (true) {

    // Clone clients' steams
    $listenA = $clientA;

    // Temporarily add server-stream sockets
    $listenA[] = $server;

    // Check if something interesting happened
    if (!stream_select($listenA, $write = array(), $except = array(), null)) break;

    // If server got new client
    if (in_array($server, $listenA)) {

        // Accept client's stream
        if (($clientI = stream_socket_accept($server, -1)) && $info = handshake($clientI)) {

            // Add to collection
            $clientA[] = $clientI;

            // Write empty json
            fwrite($clientI, encode('{}'));
        }

        // Remove server's socket from the list of sockets to be listened
        unset($listenA[array_search($server, $listenA)]);
    }

    // Foreach client stream
    foreach($listenA as $clientI) {

        // Read data
        $data = fread($clientI, 100000);

        // Get stream index
        $index = array_search($clientI, $clientA);

        // If no data
        if (!$data) {

            // Close client's current stream
            fclose($clientI);

            // Unset meta info, related to current stream
            foreach ($channelA as $rid => $byrid)
                foreach ($channelA[$rid] as $uid => $byuid)
                    unset($channelA[$rid][$uid][$index]);

            // Unset current stream
            unset($clientA[$index]); echo 'close';

            // Goto next stream
            continue;
        }

        // Decode data
        $data = decode($data);

        // Here we skip messages having 'type' not 'text'
        if ($data['type'] != 'text') continue;

        // Convert json-decoded payload into an array
        $data = json_decode($data['payload'], true);

        // If some user connected
        if ($data['type'] == 'open') {

            // Get user's role id and self id
            list($rid, $uid) = explode('-', $data['uid']);

            // Organize channels
            if (!is_array($channelA[$rid])) $channelA[$rid] = array();
            if (!is_array($channelA[$rid][$uid])) $channelA[$rid][$uid] = array();
            $channelA[$rid][$uid][$index] = $index;

        // Else
        } else if ($data['type'] == 'notice') {

            // Walk through roles, that recipients should have
            // If there are channels already exist for recipients, having such role
            foreach ($data['to'] as $rid => $uidA) if ($channelA[$rid]) {

                // If all recipients having such role should be messaged
                // Send message to all recipients having such role
                if ($data['to'][$rid] === true) foreach ($channelA[$rid] as $uid => $byrole)
                    foreach ($channelA[$rid][$uid] as $cid)
                        fwrite($clientA[$channelA[$rid][$uid][$cid]], encode(json_encode($data)));

                // Else if we have certain list of recipients
                // Send message to certain recipients
                else foreach ($data['to'][$rid] as $uid)
                    if ($channelA[$rid][$uid])
                        foreach ($channelA[$rid][$uid] as $cid)
                            fwrite($clientA[$channelA[$rid][$uid][$cid]], encode(json_encode($data)));
            }
        }
    }
}

/**
 * @param $clientI
 * @return array|bool
 */
function handshake($clientI) {

    // Client's stream info
    $info = array();

    // Get request's 1st line
    $line = fgets($clientI);

    // Get request's method and URI
    $hdr = explode(' ', $line); $info['method'] = $hdr[0]; $info['uri'] = $hdr[1];

    // Get headers
    while ($line = rtrim(fgets($clientI)))
        if (preg_match('/\A(\S+): (.*)\z/', $line, $m))
            $info[$m[1]] = $m[2]; else break;

    // Get client's addr
    $addr = explode(':', stream_socket_get_name($clientI, true)); $info['ip'] = $addr[0]; $info['port'] = $addr[1];

    // If no 'Sec-WebSocket-Key' header provided - return false
    if (empty($info['Sec-WebSocket-Key'])) return false;

    // Prepare value for 'Sec-WebSocket-Accept' header
    $SecWebSocketAccept = base64_encode(pack('H*', sha1($info['Sec-WebSocket-Key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

    // Prepare full headers list
    $upgrade = implode("\r\n", array(
        'HTTP/1.1 101 Web Socket Protocol Handshake',
        'Upgrade: websocket',
        'Connection: Upgrade',
        'Sec-WebSocket-Accept: ' . $SecWebSocketAccept
    )) . "\r\n\r\n";

    // Write upgrade headers into client's stream
    fwrite($clientI, $upgrade);

    // Return request info
    return $info;
}

/**
 * @param $payload
 * @param string $type
 * @param bool $masked
 * @return array|string
 */
function encode($payload, $type = 'text', $masked = false) {

    // Frame head
    $fh = array();

    // Get payload length
    $payloadLength = strlen($payload);

    // Frame types
    $typeA = array('text' => 129, 'close' => 136, 'ping' => 137, 'pong' => 138);

    // Start setting up frame head
    $fh[0] = $typeA[$type];

    // If payload length is greater than 65kb
    if ($payloadLength > 65535) {

        // Setup payload binary length
        $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);

        // Finish setting up frame head
        $fh[1] = ($masked === true) ? 255 : 127; for ($i = 0; $i < 8; $i++) $fh[$i + 2] = bindec($payloadLengthBin[$i]);
        if ($fh[2] > 127) return array('type' => '', 'payload' => '', 'error' => 'frame too large (1004)');

    // Else if payload length is greater than 125b
    } else if ($payloadLength > 125) {

        // Setup payload binary length
        $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);

        // Finish setting up frame head
        $fh[1] = ($masked === true) ? 254 : 126;
        $fh[2] = bindec($payloadLengthBin[0]);
        $fh[3] = bindec($payloadLengthBin[1]);

    // Else
    } else $fh[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;

    // Convert frame head to string
    foreach (array_keys($fh) as $i) $fh[$i] = chr($fh[$i]);

    // If masked
    if ($masked === true) {

        // Generate mask:
        $mask = array(); for ($i = 0; $i < 4; $i++) $mask[$i] = chr(rand(0, 255));

        // Append
        $fh = array_merge($fh, $mask);
    }

    // Stringify frame head
    $frame = implode('', $fh);

    // Append payload
    for ($i = 0; $i < $payloadLength; $i++) $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];

    // Return
    return $frame;
}

/**
 * @param $data
 * @return array|bool
 */
function decode($data) {

    // Decoded data array
    $decoded = array('payload' => '');

    // Detect opcode using first byte
    $fbb = sprintf('%08b', ord($data[0]));
    $opcode = bindec(substr($fbb, 4, 4));

    // Detect whether or not data is masked using second byte binary
    $sbb = sprintf('%08b', ord($data[1]));
    $masked = $sbb[0] == '1';

    // Detect payload length
    $payloadLength = ord($data[1]) & 127;

    // If unmasked frame data received - return error msg
    if (!$masked) return array('type' => '', 'payload' => '', 'error' => 'protocol error (1002)');

    // Try to detect frame type, or return error msg
    $typeA = array(1 => 'text', 2 => 'binary', 8 => 'close', 9 => 'ping', 10 => 'pong');
    if (!$decoded['type'] = $typeA[$opcode])
        return array('type' => '', 'payload' => '', 'error' => 'unknown opcode (1003)');

    // If payload length is 126
    if ($payloadLength === 126) {

        // Get mask
        $mask = substr($data, 4, 4);

        // Setup payload offset
        $payloadOffset = 8;

        // Detect data length
        $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;

    // If payload length is 127
    } else if ($payloadLength === 127) {

        // Get mask
        $mask = substr($data, 10, 4);

        // Setup payload offset
        $payloadOffset = 14;

        // Detect data length
        $tmp = ''; for ($i = 0; $i < 8; $i++) $tmp .= sprintf('%08b', ord($data[$i + 2]));
        $dataLength = bindec($tmp) + $payloadOffset; unset($tmp);

    // Else
    } else {

        // Get mask
        $mask = substr($data, 2, 4);

        // Setup payload offset
        $payloadOffset = 6;

        // Detect data length
        $dataLength = $payloadLength + $payloadOffset;
    }

    // We have to check for large frames here. socket_recv cuts at 1024 bytes
    // so if websocket-frame is > 1024 bytes we have to wait until whole
    // data is transferred.
    if (strlen($data) < $dataLength) return false;

    // Unmask payload
    for ($i = $payloadOffset; $i < $dataLength; $i++)
        if (isset($data[$i]))
            $decoded['payload'] .= $data[$i] ^ $mask[($i - $payloadOffset) % 4];

    // Return decoded data
    return $decoded;
}

// Shutdown
shutdown();