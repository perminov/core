<?php
class Indi {

    /**
     * An internal static variable, will be used to store data, that should be accessible anywhere
     *
     * @var array
     */
    protected static $_registry = array();

    /**
     * An internal static variable, will be used to store data, got from `staticblock` table 
	 * as an assotiative array  and that should be accessible anywhere
     *
     * @var array|null
     */
    protected static $_blockA = null;

    /**
     * Compilation template
     *
     * @var string
     */
    public static $cmpTpl = '';

    /**
     * Compilation result/output
     *
     * @var string
     */
    public static $cmpOut = '';

    /**
     * Regular expressions patterns for common usage
     *
     * @var array
     */
    protected static $_rex = array(
        'email' => '/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/',
        'date' => '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/',
        'zerodate' => '/^[0\.\-\/ ]*$/',
        'year' => '/^[0-9]{4}$/',
        'hrgb' => '/^[0-9]{3}#([0-9a-fA-F]{6})$/',
        'rgb' => '/^#([0-9a-fA-F]{6})$/',
        'htmleventattr' => 'on[a-zA-Z]+\s*=\s*"[^"]+"',
        'php' => '/<\?/',
        'phpsplit' => '/(<\?|\?>)/',
        'int11' => '/^[1-9][0-9]{0,10}|0$/',
        'int11lz' => '/^[0-9]{1,11}$/',
        'int11list' => '/^[1-9][0-9]{0,10}(,[1-9][0-9]{0,10})*$/',
        'bool' => '/^0|1$/',
        'time' => '/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/',
        'double72' => '/^([1-9][0-9]{0,6}|0)(\.[0-9]{1,2})?$/',
        'datetime' => '/^[0-9]{4}\-[0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',
        'url' => '/^(ht|f)tp(s?)\:\/\/(([a-zA-Z0-9\-\._]+(\.[a-zA-Z0-9\-\._]+)+)|localhost)(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&amp;%\$#_]*)?([\d\w\.\/\%\+\-\=\&amp;\?\:\\\&quot;\'\,\|\~\;]*)$/',
        'urichunk' => ''
    );

    protected static $_mime = array(

        'definitive' => array (
            'application/x-authorware-bin' => 'aab',
            'application/x-authorware-map' => 'aam',
            'application/x-authorware-seg' => 'aas',
            'text/vnd.abc' => 'abc',
            'video/animaflex' => 'afl',
            'application/x-aim' => 'aim',
            'text/x-audiosoft-intra' => 'aip',
            'application/x-navi-animation' => 'ani',
            'application/x-nokia-9000-communicator-add-on-software' => 'aos',
            'application/mime' => 'aps',
            'application/arj' => 'arj',
            'image/x-jg' => 'art',
            'text/asp' => 'asp',
            'application/x-mplayer2' => 'asx',
            'video/x-ms-asf-plugin' => 'asx',
            'audio/x-au' => 'au',
            'application/x-troff-msvideo' => 'avi',
            'video/avi' => 'avi',
            'video/msvideo' => 'avi',
            'video/x-msvideo' => 'avi',
            'video/avs-video' => 'avs',
            'application/x-bcpio' => 'bcpio',
            'application/mac-binary' => 'bin',
            'application/macbinary' => 'bin',
            'application/x-binary' => 'bin',
            'application/x-macbinary' => 'bin',
            'image/x-windows-bmp' => 'bmp',
            'application/x-bzip' => 'bz',
            'application/vnd.ms-pki.seccat' => 'cat',
            'application/clariscad' => 'ccad',
            'application/x-cocoa' => 'cco',
            'application/cdf' => 'cdf',
            'application/x-cdf' => 'cdf',
            'application/java' => 'class',
            'application/java-byte-code' => 'class',
            'application/x-java-class' => 'class',
            'application/x-cpio' => 'cpio',
            'application/mac-compactpro' => 'cpt',
            'application/x-compactpro' => 'cpt',
            'application/x-cpt' => 'cpt',
            'application/pkcs-crl' => 'crl',
            'application/pkix-crl' => 'crl',
            'application/x-x509-user-cert' => 'crt',
            'application/x-csh' => 'csh',
            'text/x-script.csh' => 'csh',
            'application/x-pointplus' => 'css',
            'text/css' => 'css',
            'application/x-deepv' => 'deepv',
            'video/dl' => 'dl',
            'video/x-dl' => 'dl',
            'application/commonground' => 'dp',
            'application/drafting' => 'drw',
            'application/x-dvi' => 'dvi',
            'drawing/x-dwf (old)' => 'dwf',
            'model/vnd.dwf' => 'dwf',
            'application/acad' => 'dwg',
            'application/dxf' => 'dxf',
            'text/x-script.elisp' => 'el',
            'application/x-bytecode.elisp (compiled elisp)' => 'elc',
            'application/x-elc' => 'elc',
            'application/x-esrehber' => 'es',
            'text/x-setext' => 'etx',
            'application/envoy' => 'evy',
            'application/vnd.fdf' => 'fdf',
            'application/fractals' => 'fif',
            'image/fif' => 'fif',
            'video/fli' => 'fli',
            'video/x-fli' => 'fli',
            'text/vnd.fmi.flexstor' => 'flx',
            'video/x-atomic3d-feature' => 'fmf',
            'image/vnd.fpx' => 'fpx',
            'image/vnd.net-fpx' => 'fpx',
            'application/freeloader' => 'frl',
            'image/g3fax' => 'g3',
            'image/gif' => 'gif',
            'video/gl' => 'gl',
            'video/x-gl' => 'gl',
            'application/x-gsp' => 'gsp',
            'application/x-gss' => 'gss',
            'application/x-gtar' => 'gtar',
            'multipart/x-gzip' => 'gzip',
            'application/x-hdf' => 'hdf',
            'text/x-script' => 'hlb',
            'application/hlp' => 'hlp',
            'application/x-winhelp' => 'hlp',
            'application/binhex' => 'hqx',
            'application/binhex4' => 'hqx',
            'application/mac-binhex' => 'hqx',
            'application/mac-binhex40' => 'hqx',
            'application/x-binhex40' => 'hqx',
            'application/x-mac-binhex40' => 'hqx',
            'application/hta' => 'hta',
            'text/x-component' => 'htc',
            'text/webviewhtml' => 'htt',
            'x-conference/x-cooltalk' => 'ice ',
            'image/x-icon' => 'ico',
            'application/x-ima' => 'ima',
            'application/x-httpd-imap' => 'imap',
            'application/inf' => 'inf ',
            'application/x-internett-signup' => 'ins',
            'application/x-ip2' => 'ip ',
            'video/x-isvideo' => 'isu',
            'audio/it' => 'it',
            'application/x-inventor' => 'iv',
            'i-world/i-vrml' => 'ivr',
            'application/x-livescreen' => 'ivy',
            'audio/x-jam' => 'jam ',
            'application/x-java-commerce' => 'jcm',
            'image/x-jps' => 'jps',
            'application/x-javascript' => 'js',
            'image/jutvision' => 'jut',
            'music/x-karaoke' => 'kar',
            'application/x-ksh' => 'ksh',
            'text/x-script.ksh' => 'ksh',
            'audio/x-liveaudio' => 'lam',
            'application/lha' => 'lha',
            'application/x-lha' => 'lha',
            'application/x-lisp' => 'lsp',
            'text/x-script.lisp' => 'lsp',
            'text/x-la-asf' => 'lsx',
            'application/x-lzh' => 'lzh',
            'application/lzx' => 'lzx',
            'application/x-lzx' => 'lzx',
            'text/x-m' => 'm',
            'audio/x-mpequrl' => 'm3u ',
            'application/x-troff-man' => 'man',
            'application/x-navimap' => 'map',
            'application/mbedlet' => 'mbd',
            'application/x-magic-cap-package-1.0' => 'mc$',
            'application/mcad' => 'mcd',
            'application/x-mathcad' => 'mcd',
            'image/vasa' => 'mcf',
            'text/mcf' => 'mcf',
            'application/netmc' => 'mcp',
            'application/x-troff-me' => 'me ',
            'application/x-frame' => 'mif',
            'application/x-mif' => 'mif',
            'www/mime' => 'mime',
            'audio/x-vnd.audioexplosion.mjuicemediafile' => 'mjf',
            'video/x-motion-jpeg' => 'mjpg',
            'application/x-meme' => 'mm',
            'audio/mod' => 'mod',
            'audio/x-mod' => 'mod',
            'audio/x-mpeg' => 'mp2',
            'video/x-mpeq2a' => 'mp2',
            'audio/mpeg3' => 'mp3',
            'audio/x-mpeg-3' => 'mp3',
            'application/vnd.ms-project' => 'mpp',
            'application/marc' => 'mrc',
            'application/x-troff-ms' => 'ms',
            'application/x-vnd.audioexplosion.mzz' => 'mzz',
            'application/vnd.nokia.configuration-message' => 'ncm',
            'application/x-mix-transfer' => 'nix',
            'application/x-conference' => 'nsc',
            'application/x-navidoc' => 'nvd',
            'application/oda' => 'oda',
            'application/x-omc' => 'omc',
            'application/x-omcdatamaker' => 'omcd',
            'application/x-omcregerator' => 'omcr',
            'text/x-pascal' => 'p',
            'application/pkcs10' => 'p10',
            'application/x-pkcs10' => 'p10',
            'application/pkcs-12' => 'p12',
            'application/x-pkcs12' => 'p12',
            'application/x-pkcs7-signature' => 'p7a',
            'application/x-pkcs7-certreqresp' => 'p7r',
            'application/pkcs7-signature' => 'p7s',
            'text/pascal' => 'pas',
            'image/x-portable-bitmap' => 'pbm',
            'application/vnd.hp-pcl' => 'pcl',
            'application/x-pcl' => 'pcl',
            'image/x-pict' => 'pct',
            'image/x-pcx' => 'pcx',
            'application/pdf' => 'pdf',
            'audio/make.my.funk' => 'pfunk',
            'image/x-portable-graymap' => 'pgm',
            'image/x-portable-greymap' => 'pgm',
            'application/x-newton-compatible-pkg' => 'pkg',
            'application/vnd.ms-pki.pko' => 'pko',
            'text/x-script.perl' => 'pl',
            'application/x-pixclscript' => 'plx',
            'text/x-script.perl-module' => 'pm',
            'application/x-portable-anymap' => 'pnm',
            'image/x-portable-anymap' => 'pnm',
            'model/x-pov' => 'pov',
            'image/x-portable-pixmap' => 'ppm',
            'application/powerpoint' => 'ppt',
            'application/x-mspowerpoint' => 'ppt',
            'application/x-freelance' => 'pre',
            'paleovu/x-pv' => 'pvu',
            'text/x-script.phyton' => 'py',
            'applicaiton/x-bytecode.python' => 'pyc',
            'audio/vnd.qcelp' => 'qcp',
            'video/x-qtc' => 'qtc',
            'audio/x-realaudio' => 'ra',
            'application/x-cmu-raster' => 'ras',
            'image/x-cmu-raster' => 'ras',
            'text/x-script.rexx' => 'rexx',
            'image/vnd.rn-realflash' => 'rf',
            'image/x-rgb' => 'rgb',
            'application/vnd.rn-realmedia' => 'rm',
            'audio/mid' => 'rmi',
            'application/ringing-tones' => 'rng',
            'application/vnd.nokia.ringing-tone' => 'rng',
            'application/vnd.rn-realplayer' => 'rnx',
            'image/vnd.rn-realpix' => 'rp',
            'text/vnd.rn-realtext' => 'rt',
            'application/x-rtf' => 'rtf',
            'video/vnd.rn-realvideo' => 'rv',
            'audio/s3m' => 's3m',
            'application/x-lotusscreencam' => 'scm',
            'text/x-script.guile' => 'scm',
            'text/x-script.scheme' => 'scm',
            'video/x-scm' => 'scm',
            'application/sdp' => 'sdp',
            'application/x-sdp' => 'sdp',
            'application/sounder' => 'sdr',
            'application/sea' => 'sea',
            'application/x-sea' => 'sea',
            'application/set' => 'set',
            'application/x-sh' => 'sh',
            'text/x-script.sh' => 'sh',
            'audio/x-psid' => 'sid',
            'application/x-sit' => 'sit',
            'application/x-stuffit' => 'sit',
            'application/x-seelogo' => 'sl',
            'audio/x-adpcm' => 'snd',
            'application/solids' => 'sol',
            'application/x-pkcs7-certificates' => 'spc',
            'application/futuresplash' => 'spl',
            'application/streamingmedia' => 'ssm',
            'application/vnd.ms-pki.certstore' => 'sst',
            'application/sla' => 'stl',
            'application/vnd.ms-pki.stl' => 'stl',
            'application/x-navistyle' => 'stl',
            'application/x-sv4cpio' => 'sv4cpio',
            'application/x-sv4crc' => 'sv4crc',
            'x-world/x-svr' => 'svr',
            'application/x-shockwave-flash' => 'swf',
            'application/x-tar' => 'tar',
            'application/toolbook' => 'tbk',
            'application/x-tcl' => 'tcl',
            'text/x-script.tcl' => 'tcl',
            'text/x-script.tcsh' => 'tcsh',
            'application/x-tex' => 'tex',
            'application/plain' => 'text',
            'application/gnutar' => 'tgz',
            'audio/tsp-audio' => 'tsi',
            'application/dsptype' => 'tsp',
            'audio/tsplayer' => 'tsp',
            'text/tab-separated-values' => 'tsv',
            'text/x-uil' => 'uil',
            'application/i-deas' => 'unv',
            'application/x-ustar' => 'ustar',
            'multipart/x-ustar' => 'ustar',
            'application/x-cdlink' => 'vcd',
            'text/x-vcalendar' => 'vcs',
            'application/vda' => 'vda',
            'video/vdo' => 'vdo',
            'application/groupwise' => 'vew',
            'application/vocaltec-media-desc' => 'vmd',
            'application/vocaltec-media-file' => 'vmf',
            'audio/voc' => 'voc',
            'audio/x-voc' => 'voc',
            'video/vosaic' => 'vos',
            'audio/voxware' => 'vox',
            'audio/x-twinvq' => 'vqf',
            'application/x-vrml' => 'vrml',
            'x-world/x-vrt' => 'vrt',
            'application/wordperfect6.1' => 'w61',
            'audio/wav' => 'wav',
            'audio/x-wav' => 'wav',
            'application/x-qpro' => 'wb1',
            'image/vnd.wap.wbmp' => 'wbmp',
            'application/vnd.xara' => 'web',
            'application/x-123' => 'wk1',
            'windows/metafile' => 'wmf',
            'text/vnd.wap.wml' => 'wml',
            'application/vnd.wap.wmlc' => 'wmlc',
            'text/vnd.wap.wmlscript' => 'wmls',
            'application/vnd.wap.wmlscriptc' => 'wmlsc',
            'application/x-wpwin' => 'wpd',
            'application/x-lotus' => 'wq1',
            'application/mswrite' => 'wri',
            'application/x-wri' => 'wri',
            'text/scriplet' => 'wsc',
            'application/x-wintalk' => 'wtk',
            'image/x-xbitmap' => 'xbm',
            'image/x-xbm' => 'xbm',
            'image/xbm' => 'xbm',
            'video/x-amt-demorun' => 'xdr',
            'xgl/drawing' => 'xgz',
            'image/vnd.xiff' => 'xif',
            'audio/xm' => 'xm',
            'application/xml' => 'xml',
            'text/xml' => 'xml',
            'xgl/movie' => 'xmz',
            'application/x-vnd.ls-xpix' => 'xpix',
            'image/xpm' => 'xpm',
            'video/x-amt-showrun' => 'xsr',
            'image/x-xwd' => 'xwd',
            'image/x-xwindowdump' => 'xwd',
            'application/x-compress' => 'z',
            'application/x-zip-compressed' => 'zip',
            'application/zip' => 'zip',
            'multipart/x-zip' => 'zip',
            'text/x-script.zsh' => 'zsh'
        ),

        'ambiguous' => array(
            'x-world/x-3dmf' => array('3dm', '3dmf', 'qd3', 'qd3d'),
            'application/octet-stream' => array(
                'a', 'arc', 'arj', 'bin', 'com', 'dump', 'exe', 'lha',
                'lhx', 'lzh', 'lzx', 'o', 'psd', 'saveme', 'uu', 'zoo'),
            'text/html' => array('html', 'acgi', 'htm', 'htmls', 'htx', 'shtml'),
            'application/postscript' => array('ps','ai', 'eps'),
            'audio/aiff' => array('aif', 'aifc', 'aiff'),
            'audio/x-aiff' => array('aiff', 'aifc', 'aif'),
            'video/x-ms-asf' => array('asf', 'asx'),
            'text/x-asm' => array('asm', 's'),
            'audio/basic' => array('au', 'snd'),
            'image/bmp' => array('bmp', 'bm'),
            'application/book' => array('boo', 'book'),
            'application/x-bzip2' => array('bz2','boz'),
            'application/x-bsh' => array('bsh','sh','shar'),
            'text/plain' => array('txt','c','c++','cc','conf','cxx','def','f','f90','for','g','h','hh','idc','jav','java','list','log','lst','m','mar','pl','sdml','text'),
            'text/x-c' => array('c','cc','cpp'),
            'application/x-netcdf' => array('cdf','nc'),
            'application/pkix-cert' => array('cer','crt'),
            'application/x-x509-ca-cert' => array('cer','crt','der'),
            'application/x-chat' => array('cha','chat'),
            'application/x-director' => array('dcr','dir','dxr'),
            'video/x-dv' => array('dif','dv'),
            'application/msword' => array('doc','dot','w6w','wiz','word'),
            'image/vnd.dwg' => array('dwg','dxf','svf'),
            'image/x-dwg' => array('dwg','dxf','svf'),
            'application/x-envoy' => array('env','evy'),
            'text/x-fortran' => array('f','f77','f90','for'),
            'image/florian' => array('flo','turbot'),
            'audio/make' => array('funk','my','pfunk'),
            'audio/x-gsm' => array('gsd','gsm'),
            'application/x-compressed' => array('gz','tgz','z','zip'),
            'application/x-gzip' => array('gz','gzip'),
            'text/x-h' => array('h','hh'),
            'application/x-helpfile' => array('help','hlp'),
            'application/vnd.hp-hpgl' => array('hgl','hpg','hpgl'),
            'image/ief' => array('ief','iefs'),
            'application/iges' => array('iges','igs'),
            'model/iges' => array('iges','igs'),
            'text/x-java-source' => array('java','jav'),
            'image/jpeg' => array('jpg','jfif','jfif-tbnl','jpe','jpeg'),
            'image/pjpeg' => array('jfif','jpe','jpeg','jpg'),
            'audio/midi' => array('kar','mid','midi'),
            'audio/nspaudio' => array('la','lma'),
            'audio/x-nspaudio' => array('la','lma'),
            'application/x-latex' => array('latex ','ltx'),
            'video/mpeg' => array('mpeg','m1v','m2v','mp2','mp3','mpa','mpe','mpg','mpeg4'),
            'audio/mpeg' => array('m2a','mp2','mpa','mpg','mpga'),
            'message/rfc822' => array('mime','mht','mhtml'),
            'application/x-midi' => array('mid','midi'),
            'audio/x-mid' => array('mid','midi'),
            'audio/x-midi' => array('mid','midi'),
            'music/crescendo' => array('mid','midi'),
            'x-music/x-midi' => array('mid','midi'),
            'application/base64' => array('mm','mme'),
            'video/quicktime' => array('mov','moov','qt'),
            'video/x-sgi-movie' => array('movie','mv'),
            'video/x-mpeg' => array('mp4', 'mp2', 'mp3'),
            'application/x-project' => array ('mpt','mpv','mpx'),
            'image/naplps' => array('nap','naplps'),
            'image/x-niff' => array ('niff'),
            'application/pkcs7-mime' => array('p7c'),
            'application/x-pkcs7-mime' => array('p7c','p7m'),
            'application/pro_eng' => array('part','prt'),
            'chemical/x-pdb' => array('pdb','xyz'),
            'image/pict' => array('pic','pict'),
            'image/x-xpixmap' => array('pm','xpm'),
            'application/x-pagemaker' => array('pm4','pm5'),
            'image/png' => array('png','x-png'),
            'application/mspowerpoint' => array('ppt','pot','pps','ppz'),
            'application/vnd.ms-powerpoint' => array('ppt','pot','ppa','pps','pwz'),
            'image/x-quicktime' => array('qtif'),
            'audio/x-pn-realaudio' => array('ra','ram','rm','rmm','rmp'),
            'audio/x-pn-realaudio-plugin' => array('ra','rmp','rpm'),
            'image/cmu-raster' => array('ras','rast'),
            'application/x-troff' => array ('t','tr'),
            'text/richtext' => array('rtf','rt','rtx'),
            'application/rtf' => array('rtf','rtx'),
            'application/x-tbook' => array('sbk ','tbk'),
            'text/sgml' => array('sgml'),
            'text/x-sgml' => array('sgm','sgml'),
            'application/x-shar' => array('sh','shar'),
            'text/x-server-parsed-html' => array('shtml','ssi'),
            'application/x-koan' => array('skd','skm','skt'),
            'application/smil' => array('smi','smil'),
            'text/x-speech' => array('spc','talk'),
            'application/x-sprite' => array('spr','sprite'),
            'application/x-wais-source' => array('src'),
            'application/step' => array('step','stp'),
            'application/x-world' => array('svr','wrl'),
            'application/x-texinfo' => array('texi','texinfo'),
            'image/tiff' => array('tif','tiff'),
            'image/x-tiff' => array('tif','tiff'),
            'text/uri-list' => array('uni','unis','uri','uris'),
            'text/x-uuencode' => array('uu','uue'),
            'video/vivo' => array('viv','vivo'),
            'video/vnd.vivo' => array('viv','vivo'),
            'audio/x-twinvq-plugin' => array('vqe','vql'),
            'model/vrml' => array('vrml','wrl','wrz'),
            'x-world/x-vrml' => array('vrml','wrl','wrz'),
            'application/x-visio' => array('vsd','vst','vsw'),
            'application/wordperfect6.0' => array('w60','wp5'),
            'application/wordperfect' => array('wp','wp5','wp6','wpd'),
            'application/excel' => array('xls','xl','xla','xlb','xlc','xld','xlk','xll','xlm','xlt','xlv','xlw'),
            'application/x-excel' => array('xls','xla','xlb','xlc','xld','xlk','xll','xlm','xlt','xlv','xlw'),
            'application/x-msexcel' => array('xls','xla','xlw'),
            'application/vnd.ms-excel' => array('xls','xlb','xlc','xll','xlm','xlw')
        )
    );


    /**
     * Array of HTML colors
     *
     * @var array
     */
    public static $colorNameA = array(
        'aliceblue'=>'F0F8FF',
        'antiquewhite'=>'FAEBD7',
        'aqua'=>'00FFFF',
        'aquamarine'=>'7FFFD4',
        'azure'=>'F0FFFF',
        'beige'=>'F5F5DC',
        'bisque'=>'FFE4C4',
        'black'=>'000000',
        'blanchedalmond '=>'FFEBCD',
        'blue'=>'0000FF',
        'blueviolet'=>'8A2BE2',
        'brown'=>'A52A2A',
        'burlywood'=>'DEB887',
        'cadetblue'=>'5F9EA0',
        'chartreuse'=>'7FFF00',
        'chocolate'=>'D2691E',
        'coral'=>'FF7F50',
        'cornflowerblue'=>'6495ED',
        'cornsilk'=>'FFF8DC',
        'crimson'=>'DC143C',
        'cyan'=>'00FFFF',
        'darkblue'=>'00008B',
        'darkcyan'=>'008B8B',
        'darkgoldenrod'=>'B8860B',
        'darkgray'=>'A9A9A9',
        'darkgreen'=>'006400',
        'darkgrey'=>'A9A9A9',
        'darkkhaki'=>'BDB76B',
        'darkmagenta'=>'8B008B',
        'darkolivegreen'=>'556B2F',
        'darkorange'=>'FF8C00',
        'darkorchid'=>'9932CC',
        'darkred'=>'8B0000',
        'darksalmon'=>'E9967A',
        'darkseagreen'=>'8FBC8F',
        'darkslateblue'=>'483D8B',
        'darkslategray'=>'2F4F4F',
        'darkslategrey'=>'2F4F4F',
        'darkturquoise'=>'00CED1',
        'darkviolet'=>'9400D3',
        'deeppink'=>'FF1493',
        'deepskyblue'=>'00BFFF',
        'dimgray'=>'696969',
        'dimgrey'=>'696969',
        'dodgerblue'=>'1E90FF',
        'firebrick'=>'B22222',
        'floralwhite'=>'FFFAF0',
        'forestgreen'=>'228B22',
        'fuchsia'=>'FF00FF',
        'gainsboro'=>'DCDCDC',
        'ghostwhite'=>'F8F8FF',
        'gold'=>'FFD700',
        'goldenrod'=>'DAA520',
        'gray'=>'808080',
        'green'=>'008000',
        'greenyellow'=>'ADFF2F',
        'grey'=>'808080',
        'honeydew'=>'F0FFF0',
        'hotpink'=>'FF69B4',
        'indianred'=>'CD5C5C',
        'indigo'=>'4B0082',
        'ivory'=>'FFFFF0',
        'khaki'=>'F0E68C',
        'lavender'=>'E6E6FA',
        'lavenderblush'=>'FFF0F5',
        'lawngreen'=>'7CFC00',
        'lemonchiffon'=>'FFFACD',
        'lightblue'=>'ADD8E6',
        'lightcoral'=>'F08080',
        'lightcyan'=>'E0FFFF',
        'lightgoldenrodyellow'=>'FAFAD2',
        'lightgray'=>'D3D3D3',
        'lightgreen'=>'90EE90',
        'lightgrey'=>'D3D3D3',
        'lightpink'=>'FFB6C1',
        'lightsalmon'=>'FFA07A',
        'lightseagreen'=>'20B2AA',
        'lightskyblue'=>'87CEFA',
        'lightslategray'=>'778899',
        'lightslategrey'=>'778899',
        'lightsteelblue'=>'B0C4DE',
        'lightyellow'=>'FFFFE0',
        'lime'=>'00FF00',
        'limegreen'=>'32CD32',
        'linen'=>'FAF0E6',
        'magenta'=>'FF00FF',
        'maroon'=>'800000',
        'mediumaquamarine'=>'66CDAA',
        'mediumblue'=>'0000CD',
        'mediumorchid'=>'BA55D3',
        'mediumpurple'=>'9370D0',
        'mediumseagreen'=>'3CB371',
        'mediumslateblue'=>'7B68EE',
        'mediumspringgreen'=>'00FA9A',
        'mediumturquoise'=>'48D1CC',
        'mediumvioletred'=>'C71585',
        'midnightblue'=>'191970',
        'mintcream'=>'F5FFFA',
        'mistyrose'=>'FFE4E1',
        'moccasin'=>'FFE4B5',
        'navajowhite'=>'FFDEAD',
        'navy'=>'000080',
        'oldlace'=>'FDF5E6',
        'olive'=>'808000',
        'olivedrab'=>'6B8E23',
        'orange'=>'FFA500',
        'orangered'=>'FF4500',
        'orchid'=>'DA70D6',
        'palegoldenrod'=>'EEE8AA',
        'palegreen'=>'98FB98',
        'paleturquoise'=>'AFEEEE',
        'palevioletred'=>'DB7093',
        'papayawhip'=>'FFEFD5',
        'peachpuff'=>'FFDAB9',
        'peru'=>'CD853F',
        'pink'=>'FFC0CB',
        'plum'=>'DDA0DD',
        'powderblue'=>'B0E0E6',
        'purple'=>'800080',
        'red'=>'FF0000',
        'rosybrown'=>'BC8F8F',
        'royalblue'=>'4169E1',
        'saddlebrown'=>'8B4513',
        'salmon'=>'FA8072',
        'sandybrown'=>'F4A460',
        'seagreen'=>'2E8B57',
        'seashell'=>'FFF5EE',
        'sienna'=>'A0522D',
        'silver'=>'C0C0C0',
        'skyblue'=>'87CEEB',
        'slateblue'=>'6A5ACD',
        'slategray'=>'708090',
        'slategrey'=>'708090',
        'snow'=>'FFFAFA',
        'springgreen'=>'00FF7F',
        'steelblue'=>'4682B4',
        'tan'=>'D2B48C',
        'teal'=>'008080',
        'thistle'=>'D8BFD8',
        'tomato'=>'FF6347',
        'turquoise'=>'40E0D0',
        'violet'=>'EE82EE',
        'wheat'=>'F5DEB3',
        'white'=>'FFFFFF',
        'whitesmoke'=>'F5F5F5',
        'yellow'=>'FFFF00',
        'yellowgreen'=>'9ACD32'
    );

    /**
     * Compilation function source code, that will be passed to eval() function. Usage:
     * // 1. Setup a template for compiling
     * Indi::$cmpTpl = 'Hello <?=$user->firstName?>';
     * // 2. Call eval() within a scope, where $user object was defined. After eval() is finished, Indi::$cmpTpl is set to ''
     * eval(Indi::$cmpRun);
     * // 3. Get a compilation result
     * $compilationResult = Indi::cmpOut();
     *
     * @var string
     */
    public static $cmpRun = '
        $iterator = \'i\' . md5(microtime() . rand(0, 100000000));
        if (preg_match(\'/<\?|\?>/\', Indi::$cmpTpl)) {
            $php = preg_split(\'/(<\?|\?>)/\', Indi::$cmpTpl, -1, PREG_SPLIT_DELIM_CAPTURE);
            Indi::$cmpOut[$iterator] = \'\';
            for ($$iterator = 0; $$iterator < count($php); $$iterator++) {
                if ($php[$$iterator] == \'<?\') {
                    $php[$$iterator+1] = preg_replace(\'/^=/\', \' echo \', $php[$$iterator+1]) . \';\';
                    ob_start(); eval($php[$$iterator+1]); Indi::$cmpOut[$iterator] .= ob_get_clean();
                    $$iterator += 2;
                } else {
                    Indi::$cmpOut[$iterator] .= $php[$$iterator];
                }
            }
        } else if (preg_match(\'/(\$|::)/\', Indi::$cmpTpl)) {
            if (preg_match(\'/^\\\'/\', trim(Indi::$cmpTpl))) {
                Indi::$cmpTpl = ltrim(Indi::$cmpTpl, "\' ");
                if (preg_match(\'/\\\'$/\', trim(Indi::$cmpTpl)))
                    Indi::$cmpTpl = rtrim(Indi::$cmpTpl, "\' ");
                eval(\'Indi::$cmpOut[$iterator] = \\\'\' . Indi::$cmpTpl . \'\\\';\');
            } else {
                eval(\'Indi::$cmpOut[$iterator] = \\\'\' . Indi::$cmpTpl . \'\\\';\');
            }
        } else {
            Indi::$cmpOut[$iterator] = Indi::$cmpTpl;
        }
        Indi::$cmpTpl = \'\';
        ';

    /**
     * Pick the last item (containing last compiled value) from self::$cmpOut array, and reduce that array,
     * so it act like a stack
     *
     * @static
     * @return mixed
     */
    public static function cmpOut() {
        return array_pop(self::$cmpOut);
    }

    /**
     * Compiles a given template. This function should be called only in case if there is no context variables mentioned
     * in template, because otherwise there will be a fatal error with messages like 'Using $this when not in object
     * context' or 'Call to a member function somefunc() on a non-object'
     *
     * @static
     * @param $tpl
     * @return string
     */
    public static function cmp($tpl){
        $out = '';
        if (preg_match('/<\?|\?>/', $tpl)) {
            $php = preg_split('/(<\?|\?>)/', $tpl, -1, PREG_SPLIT_DELIM_CAPTURE);
            for ($i = 0; $i < count($php); $i++) {
                if ($php[$i] == '<?') {
                    $php[$i+1] = preg_replace('/^=/', ' echo ', $php[$i+1]) . ';';
                    ob_start(); eval($php[$i+1]); $out .= ob_get_clean();
                    $i += 2;
                } else {
                    $out .= $php[$i];
                }
            }
        } else if (preg_match('/(\$|::)/', $tpl)) {
            eval('$out = \'' . $tpl . '\';');
        } else {
            $out = $tpl;
        }

        return $out;
    }

    /**
     * Function is similar as jQuery .attr() function.
     * If only $key param is passed, the assigned value will be returned.
     * Otherwise, if $value param is also passed, this value will be placed in self::$_registry under $key key
     *
     * @static
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function registry($key = null, $value = null) {
        // If only $key param passed, the assigned registry value will be returned
        if (func_num_args() == 1) return self::$_registry[$key];

        // Else if $value argument was given - it will be placed into registry under passed $key param.
        // If $value argument is an array, it will be converted to a new instance of ArrayObject class,
        // with setting ArrayObject::ARRAY_AS_PROPS flag for that newly created instance properties
        // to be also accessible as if they were an array elements
        else if (func_num_args() == 2)
            return self::$_registry[$key] = is_array($value)
                ? new ArrayObject($value, ArrayObject::ARRAY_AS_PROPS)
                : $value;

        // Else if no arguments passed, return the whole registry
        else if (func_num_args() == 0) return self::$_registry;
    }

    /**
     * Shortcut for Indi_Db::model() function
     * Loads the model by model's entity's id, or model class name
     *
     * @static
     * @param int|string $identifier
     * @return Indi_Db_Table object
     */
    public static function model($identifier) {

        // Call same method within Indi_Db object
        return Indi_Db::model($identifier);
    }

    /**
     * Shortcut for Indi_Db::factory() function
     * Returns an singleton instance of Indi_Db
     * If an argument is presented, it will be passed to Indi_Db::factory() method, for, in it's turn,
     * usage as PDO connection properties
     *
     * @static
     * @return Indi_Db object
     */
    public static function db() {

        // Call 'factory' method of Indi_Db class, with first argument, if given. Otherwise just Indi_Db instance
        // will be returned, with no PDO configuration setup
        return Indi_Db::factory(func_num_args() ? func_get_arg(0) : null);
    }

    /**
     * Set or get values of all uri params or single param. If there is no value for 'uri' key in registry yet, setup it
     *
     * @static
     * @param null $key
     * @param null $value
     * @return mixed|null
     */
    public static function uri($key = null, $value = null){

        // If there is no value for 'uri' key in registry yet, we setup it
        if (is_null(Indi::store('uri'))) {

            // Create an *_Uri object
            $obj = class_exists('Project_Uri') ? new Project_Uri() : new Indi_Uri();

            // Push $obj object in registry under 'uri' key
            Indi::store('uri', $obj);
        }

        // If $key argument is null or not given, return value, stored under 'uri' key in registry
        if (is_null($key)) return Indi::store('uri');

        // Else if $key argument is not null and it is the single argument passed
        if (func_num_args() == 1)

            // If $key argument is an array or is an object - return value, stored under 'uri' key in registry,
            // Else we assume it is a property name within object, stored under 'uri' key in registry, so we
            // return value of $key key
            return is_array($key) || is_object($key) ? Indi::store('uri') : Indi::registry('uri')->$key;

        // Else if $value argument is given, we assign it to $key key within data, stored under 'uri' key in registry
        if (func_num_args() == 2) return Indi::registry('uri')->$key = $value;
    }

    /**
     * Short-hand access for current user object
     *
     * @static
     * @return mixed|null
     */
    public static function user(){

        // If there is no value for 'uri' key in registry yet, we setup it
        if (is_null(Indi::store('user'))) {

            // Get the current user row
            $userR = (int) $_SESSION['user']['id']
                ? Indi::model('User')->fetchRow('`id` = "' . (int) $_SESSION['user']['id'] . '"')
                : false;

            // Push $obj object in registry under 'uri' key
            Indi::store('user', $userR);
        }

        // Return current user object
        return Indi::store('user');
    }

    /**
     * Short-hand access for current cms user (admin) object
     *
     * @static
     * @return mixed|null
     */
    public static function admin(){

        // If there is no value for 'uri' key in registry yet, we setup it
        if (is_null(Indi::store('admin'))) {

            // Get the database table name, where current cms user was found in
            $table = $_SESSION['admin']['alternate'] ? $_SESSION['admin']['alternate'] : 'admin';

            // Get the current user row
            $adminR = (int) $_SESSION['admin']['id']
                ? Indi::model($table)->fetchRow('`id` = "' . (int) $_SESSION['admin']['id'] . '"')
                : false;

            // If current cms user was found not in 'admin' database table,  we explicilty setup foreign
            // data for 'profileId' foreign key, despite on in that other table may be not such a foreign key
            if ($table != 'admin')
                $adminR->foreign('profileId', Indi::model('Profile')->fetchRow(
                    '`entityId` = "' . Indi::model($table)->id() . '"'
                ));

            // Push $obj object in registry under 'uri' key
            Indi::store('admin', $adminR);
        }

        // Return current user object
        return Indi::store('admin');
    }

    /**
     * Implode and compress files, mentioned in $files argument, under filename, constructed with usage of $alias
     * argument. This function is a part of performance improvement policy, which consists of:
     * 1. Imploding and gz-compressing all 'js' files in one, and all 'css' files in one, under filenames, ending with
     *    '.gz.css' and 'gz.css'
     * 2. Adding a .htaccess directive for additional header 'Content-Encoding: gzip' for such files
     *
     * This policy is modified version of static content compression idea, and the difference is that 'gz.css' and
     * 'gz.js' files are ALREADY gzipped, so we-server is not forced to compress them each time it receceives a request,
     * so it just flush it as is, but with special 'Content-Encoding' header, for them to be decompressed at client-side
     *
     * @param array $files
     * @param string $alias
     * @return int
     */
    public static function implode($files = array(), $alias = '') {

        // Get the type of files, here we assume that all files in $files argument have same type
        preg_match('/\.(css|js)$/', $files[0], $ext); $ext = $ext[1];

        // Get the subdir name, relative to webroot
        $rel = '/' . $ext;

        // We set $refresh as false by default
        $refresh = false;

        // Get filename of file, containing modification times for all files that are compiled
        $mtime = DOC . STD . '/core/' . $rel . '/admin/indi.all' . ($alias ? '.' . $alias : '') . '.mtime';

        // If this file does not exists, we set $refresh as true
        if (!file_exists($mtime)) {
            $refresh = true;

        // Else
        } else {

            // Get 'mtime' file contents and convert is to json
            $json = json_decode(file_get_contents($mtime), true);

            // Append mirror files
            for($i = 0; $i < count($files); $i++)
                if (is_file(DOC . STD . '/www' . $files[$i]))
                    array_splice($files, ++$i, 0, '/../www' . $files[$i-1]);

            // If $json is not an array, or is empty array, of files, mentioned in it do not match files in $files arg
            if (!is_array($json) || !count($json) || count(array_diff($files, array_keys($json)))
                || count(array_diff(array_keys($json), $files)))

                // We set $refresh as true
                $refresh = true;

            // Else we do a final check:
            else

                // If modification time  of at least one file in $files argument, is not equal to time,
                // stored in $json for that file, we set $refresh as true
                for ($i = 0; $i < count($files); $i++)
                    if (filemtime(DOC . STD . '/core' . $files[$i]) != $json[$files[$i]]) {
                        $refresh = true;
                        break;
                    }
        }

        // If after all these checks we discovered that compilation should be refreshed
        if ($refresh) {

            // Empty $json array
            $json = array();

            // Start output buffering
            ob_start();

            // Foreach file in $files argument
            for ($i = 0; $i < count($files); $i++) {

                // Get full file name
                $file = DOC . STD . '/core' . $files[$i];

                // Collect info about that file's modification time
                $json[$files[$i]] = filemtime($file);

                // Echo that file contents
                readfile($file);

                // Echo ';' if we deal with javascript files. Also flush double newline
                echo ($ext == 'js' ? ';' : '') . "\n\n";
            }

            // Refresh 'mtime' file for current compilation
            $fp = fopen($mtime, 'w'); fwrite($fp, json_encode($json)); fclose($fp);

            // Get output
            $txt = ob_get_clean();

            // If we currently deal with 'css' files
            if ($ext == 'css') {

                // Convert relative paths, mentioned in css files to paths, relative to web root
                $txt = preg_replace('!url\((\'|)/!', 'url($1' . STD . '/', $txt);
                $txt = preg_replace('!url\(\'\.\./\.\./resources!', 'url(\'' . STD . '/library/extjs4/resources', $txt);

                // Remove comments from css
                $txt = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $txt);

                // Remove tabs, excessive spaces and newlines
                $txt = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   '), '', $txt);
            }

            // Compress compilation
            $txt = gzencode($txt, 9);

            // Refresh compilation file
            $fp = fopen(DOC . STD . '/core' . $rel . '/admin/indi.all' . ($alias ? '.' . $alias : '') . '.gz.' . $ext, 'w');
            fwrite($fp, $txt);
            fclose($fp);
        }

        // Return modification time for 'mtime' file
        return filemtime($mtime);
    }

    /**
     * Shortcut for easier access to an instance of Indi_View object, stored in registry
     *
     * @static
     * @return Indi_View|null
     */
    public static function view() {
        return Indi::store('view');
    }

    /**
     * This function does similar as Indi::registry() function, but is additionally able to set/get subkeys values for
     * data, stored in registry, if that data is of types 'array' or 'object'. Function was created to avoid almost
     * same coding for Indi::get(), Indi::post() and Indi::files() functions, so now these function use this function
     * instead of consisting of almost same code.
     *
     * @static
     * @param null $key
     * @param null $arg1
     * @param null $arg2
     * @return mixed|null
     */
    public static function store($key = null, $arg1 = null, $arg2 = null) {

        // If no $key argument was given - return whole registry
        if (is_null($key)) return Indi::registry();

        // If $key argument is not null and $arg1 argument is null - we get the value, stored in registry
        // under $key key, and return it
        if (is_null($arg1)) return Indi::registry($key);

        // Else if only $key and $arg1 arguments is passed, and they both are not null
        if (func_num_args() == 2)

            // We check is $arg1 an array or an object, and if so
            if (is_array($arg1) || is_object($arg1)) {

                // Set a value ($arg1) for $key key in registry, because the fact that $arg1 is array/object mean that
                // it is not a key, as arrays and objects are not allowed to be used as array keys or object properties
                return Indi::registry($key, $arg1);

            // Else if $arg1 argument is not an array or object, we assume that it is a subkey, so we return it's value
            } else return Indi::store($key)->$arg1;

        // Else if three arguments passed, we assume that they are key, subkey and value, so we set a value, got from
        // third argument under a subkey (second argument), under a $key key in registry and after that return that value
        else if (func_num_args() == 3) return Indi::store($key)->$arg1 = $arg2;
    }

    /**
     * Set or gets $_GET params as single param or as whole array, converted to instance of ArrayObject class.
     * Usage:
     * 1.Indi::get();               //   ArrayObject (
     *                              //       [param1] => value1
     *                              //       [param2] => value2
     *                              //   )
     * 2.Indi::get()->param1        //   value1
     * 3.Indi::get()->param1 = 1234 //   1234
     * 4.Indi::get()->param1        //   1234
     * 5.Indi::get('param1')        //   1234
     * 6.Indi::get('param1', 12345) //   12345
     * 7.Indi::get('param1')        //   12345
     * 8.$myGetCopy = Indi::get();  //   ArrayObject (
     *                              //       [param1] => 12345
     *                              //       [param2] => value2
     *                              //   )
     * 9.$myGetCopy['param1']       //   12345
     * 10. $myGetCopy->param1       //   12345
     *
     * For initial (and further, if need) setting, use Indi::get($_GET)
     *
     * @static
     * @param null $arg1
     * @param null $arg2
     * @return mixed
     */
    public static function get($arg1 = null, $arg2 = null) {
        return func_num_args() == 1 ? Indi::store('get', $arg1) : Indi::store('get', $arg1, $arg2);
    }

    /**
     * Set or gets $_POST params as single param or as whole array, converted to instance of ArrayObject class.
     * Usage - same as for Indi::get() function
     *
     * @static
     * @param null $arg1
     * @param null $arg2
     * @return mixed
     */
    public static function post($arg1 = null, $arg2 = null) {
        return func_num_args() == 1 ? Indi::store('post', $arg1) : Indi::store('post', $arg1, $arg2);
    }

    /**
     * Set or gets $_FILES params as single param or as whole array, converted to instance of ArrayObject class.
     * Usage - same as for Indi::get() function
     *
     * @static
     * @param null $arg1
     * @param null $arg2
     * @return mixed
     */
    public static function files($arg1 = null, $arg2 = null) {
        return func_num_args() == 1 ? Indi::store('files', $arg1) : Indi::store('files', $arg1, $arg2);
    }

    /**
     * Setup a proper order of elements in $setA array, depending on their titles
     *
     * @static
     * @param $entityId
     * @param $idA
     * @param string $dir
     * @return array
     */
    public static function order($entityId, $idA, $dir = 'ASC'){
        // Load the model
        $model = Indi::model($entityId);

        // Get the columns list
        $columnA = $model->fields(null, 'aliases');

        // Determine title column name
        if ($titleColumn = current(array_intersect($columnA, array('title', '_title')))) {

            // Setup a new order for $idA
            $idA = Indi::db()->query('

                SELECT `id`
                FROM `' . $model->table() . '`
                WHERE `id` IN (' . implode(',', $idA) . ')
                ORDER BY `' . $titleColumn . '` ' . $dir . '

            ')->fetchAll(PDO::FETCH_COLUMN);
        }

        // Return reordered ids
        return $idA;
    }
	
    /**
     * Return an array containing defined constants, which are lang-constants at most
     *
     * @static
     * @param boolean $json
     * @return array|json
     */
	public static function lang($json = false) {

        // Define $langA array
        $langA = array();

        // Foreach defined constants check if constant name starts with 'I_', and if so - append it to $langA array
		foreach (get_defined_constants() as $name => $value)
            if (preg_match('/^I_/', $name))
                $langA[$name] = $value;

        // Return lang constants as an array, optionally encoded to json, depending on $json argument is boolean true
		return $json ? json_encode($langA) : $langA;
	}

    /**
     * Converts an html color name to a hex color value
     *
     * @static
     * @param $color
     * @return string
     */
    public static function hexColor($color) {

        // Remove the spaces, and leading '#', if presented
        $color = ltrim(trim($color), '#');

        // If $color is a hex color in format 'rrggbb', we return it as is
        if (preg_match('/^([a-fA-F0-9]{6})$/', $color, $match)) {
            return $match[1];

        // Else if $color is a hex color, but in format 'rgb' we convert it to 'rrggbb' format
        } else if (preg_match('/^([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/', $color, $match)) {
            $hex = ''; for ($i = 1; $i < 4; $i++) $hex .= $match[$i] . $match[$i]; return $hex;

        // Else we'll try to find a match within self::$colorNameA array, containing 147 standard HTML color names
        } else {

            // Convert color name to lowercase
            $color = strtolower($color);

            // If found, return it, with '#' prefix, else return empty string
            return ($hex = self::$colorNameA[$color]) ? '#' . $hex : '';
        }
    }

	/**
	 * Fetch rowset from `staticblock` table and return it as an assotiative array with aliases as keys.
	 * Rows in `staticblock` table store some text phrases and settings, so function provide and ability to
	 * access it from anywhere. Rowset fetch will be only done at first function call.
	 *
     * @param string $key
     * @param string $default A value, that will be returned if $key will not be found in self::$_blockA array
	 * @return array
	 */
	public static function blocks($key = null, $default = null){
		// If self::$_blockA is null at the moment, we fetch it from `staticblock` table
		if (self::$_blockA === null) {

			// Setup self::$_blockA as an empty array at first
			self::$_blockA = array();

			// Fetch rowset
            $staticBlockRs = Indi::model('Staticblock')->fetchAll('`toggle` = "y"');
			
			// Setup values in self::$_blockA array under certain keys
            foreach ($staticBlockRs as $staticBlockR) {
                self::$_blockA[$staticBlockR->alias] = $staticBlockR->{'details' . ucfirst($staticBlockR->type)};
                if ($staticBlockR->type == 'textarea') self::$_blockA[$staticBlockR->alias] = nl2br(self::$_blockA[$staticBlockR->alias]);
            }
		}

		// If $key argument was specified, we return a certain value, or all array otherwise
		return $key == null ? self::$_blockA : (array_key_exists($key, self::$_blockA) ? self::$_blockA[$key] : $default);
	}

    /**
     * Parses ini file given by $arg argument, convert it from array to ArrayObject and save into the registry
     * If $arg agrument does not end with '.ini', it will be interpreted as a key, so it's value will be returned
     * If $arg argument is not given or null, the whole ini ArrayObject object, that represents ini file contents
     * will be returned
     *
     * @static
     * @param null $arg
     * @return mixed|null
     */
    public static function ini($arg = null) {

        // If $arg argument is a path end with '.ini', and file with that path exists
        if (preg_match('/\.ini$/', $arg) && is_file($arg)) {

            // Parse ini file
            $parsed = parse_ini_file($arg, true);

            // Create empty instance of stdClass
            $ini = new stdClass();

            // Foreach section
            foreach ($parsed as $section => $params) {

                // Setup section as new instance of stdClass
                $ini->$section = new stdClass;

                // Foreach section's param
                foreach ($params as $key => $value) {

                    // Get the copy of current section
                    $c = $ini->$section;

                    // Foreach dot-separated sub-key name within $key
                    foreach (explode('.', $key) as $key) {

                        // If $c->$key is not yet set - setup it as new instance of stdClass
                        if (!isset($c->$key)) $c->$key = new stdClass();

                        // Setup previous param
                        $prev = $c;

                        // Shift nesting
                        $c = $c->$key;
                    }

                    // Setup value
                    $prev->$key = $value;
                }
            }

            // Save into the registry
            return Indi::registry('ini', $ini);
        }

        // Else if $arg argument is a string, we assume that it is a key, so we return it's value
        else if (is_string($arg)) return Indi::store('ini')->$arg;

        // Else we return the whole ini object
        else if (!$arg) return Indi::store('ini');
    }

    /**
     * Return regular expressions pattern, stored within $this->_rex property under $alias key
     *
     * @param $alias
     * @return null
     */
    public static function rex($alias){
        return $alias ? self::$_rex[$alias] : null;
    }

    /**
     * Shortcut for Indi_Trail_Admin. Usage:
     *
     * Indi::trail(true) - whole Indi_Trail_Admin object
     * Indi::trail()->row/section/sections/filters/grid/etc.
     * Indi::trail(1)->row - goes to parent trail item
     *
     * @static
     * @param null $arg
     * @return mixed
     */
    public static function trail($arg = null) {

        // If $arg argument is an array, we assume that it's a route stack, so we create a new trail object and store
        // it into the registry
        if (is_array($arg)) {
            $class = 'Indi_Trail_' . ucfirst(Indi::uri()->module);
            return Indi::registry('trail', new $class($arg));
        }

        // Else if $arg argument is boolean 'true', we return the whole trail object
        else if ($arg === true) return Indi::registry('trail');

        // Else if $arg argument is not set, we return current trail item object
        else if ($arg == null) {/*d(debug_print_backtrace());*/return Indi::registry('trail')->item();}

        // Else we return item, that is at index, shifted from the last index by $arg number. The $arg argument will
        // be casted as integer by '(int)' expression in 'item()' method call
        else return Indi::registry('trail')->item($arg);
    }


    /**
     * Load cache files if need
     *
     * @static
     */
    public static function cache(){
        Indi_Cache::load();
    }

    /**
     * Build and return an image (represented by 'img' tag), related to certain row of certain entity,
     * or the certain copy of that image, if $copy argument is given.
     *
     * @static
     * @param string $entity
     * @param int $id
     * @param string $field
     * @param string $copy
     * @param array $attr
     * @return string
     */
    public static function img($entity, $id, $field, $copy = '', $attr = array()) {

        // If $copy argument is an array, we assume that it is used as $attr argument.
        // Such implementation is bit more short-handy, because expression
        // Indi::img('myentity', 123, 'imagefield', array('height' => 200)) is more friendly than
        // Indi::img('myentity', 123, 'imagefield', null, array('height' => 200))
        if (is_array($copy)) {
            $attr = $copy;
            $copy = '';
        }

        // Get the directory name
        $dir = DOC . STD . '/' . Indi::ini()->upload->path . '/' . $entity . '/';

        // If directory does not exist - return
        if (!is_dir($dir)) return;

        // Get the image full filename
        list($abs) = glob($dir . $id . '_' . $field . ($copy ? ',' . $copy : '') . '.{gif,jpeg,jpg,png}', GLOB_BRACE);

        // If no image found - return
        if (!$abs) return;

        // Setup 'src' attribute
        $attr['src'] = substr($abs, strlen(DOC)) . '?' . substr(filemtime($abs), -3);

        // Setup empty alt attribute
        if (!isset($attr['alt'])) $attr['alt'] = '';

        // Build attributes string
        $attrA = array(); foreach ($attr as $a => $v) $attrA[] = $a . '="' . str_replace('"', '\"', $v) . '"';

        // Build and return img tag
        return '<img ' . implode(' ', $attrA) . '/>';
    }

    /**
     * Build and return a shockwave flash object (represented by 'embed' tag), related to certain row of certain entity
     *
     * @static
     * @param string $entity
     * @param int $id
     * @param string $field
     * @param array $attr
     * @return string
     */
    public static function swf($entity, $id, $field, $attr = array()) {

        // Get the directory name
        $dir = DOC . STD . '/' . Indi::ini()->upload->path . '/' . $entity . '/';

        // If directory does not exist - return
        if (!is_dir($dir)) return;

        // Get the image full filename
        list($abs) = glob($dir . $id . '_' . $field . '.swf');

        // If no image found - return
        if (!$abs) return;

        // Setup 'src' attribute
        $attr['src'] = substr($abs, strlen(DOC)) . '?' . substr(filemtime($abs), -3);

        // Setup specific attributes
        $attr['type'] = 'application/x-shockwave-flash';
        $attr['pluginspace'] = 'http://www.macromedia.com/go/getflashplayer';
        $attr['play'] = 'true';
        $attr['loop'] = 'true';
        $attr['menu'] = 'true';

        // If 'width' attribute is not set or 'height' attribute is not set
        if (!$attr['width'] || !$attr['height']) {

            // Get the real size of flash object
            list($real['width'], $real['height']) = getflashsize($abs);

            // If both 'width' and 'height' attributes are not set - set them same as real width and height
            if (!$attr['width'] && !$attr['height']) $attr = array_merge($attr, $real);

            // Else if 'width' attribute was set - calculate and setup 'height' attribute
            else if ($attr['width']) $attr['height'] = ceil($real['height']/$real['width']*$attr['width']);

            // Else if 'height' attribute was set - calculate and setup 'width' attribute
            else if ($attr['height']) $attr['width'] = ceil($real['width']/$real['height']*$attr['height']);
        }

        // Build attributes string
        $attrA = array(); foreach ($attr as $a => $v) $attrA[] = $a . '="' . str_replace('"', '\"', $v) . '"';

        // Build and return img tag
        return '<embed ' . implode(' ', $attrA) . '/>';
    }

    /**
     * Get file extension by mime-type
     *
     * @static
     * @param $mime
     * @return string
     */
    public static function ext($mime) {

        // If value of $mime argument was found as a key within self::$_mime['definitive'] array - return extension
        if (isset(self::$_mime['definitive'][$mime])) return self::$_mime['definitive'][$mime];

        // Else if value of $mime argument was found as a key within self::$_mime['ambiguous'] array - return first extension
        else if (isset(self::$_mime['ambiguous'][$mime])) return self::$_mime['ambiguous'][$mime][0];

        // Else if still no extension got - return 'unknown'
        else return 'unknown';
    }
}