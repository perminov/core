<html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/js/admin/jssip/jssip.css"/>
<script src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/admin/jssip/jssip-0.7.23.js"></script>
<script src="/js/admin/jssip/gui2.js"></script>
</head>
<body>
<script>
var $$ = document.getElementById.bind(document);
soundPlayer = document.createElement("audio");
soundPlayer.volume = 1;

$(document).ready(function(){

});


var session;

/*function dial () {
  if (!$$('target').value) {
    return;
  }

  setupSession( ua.invite($$('target').value, getSessionOptions()) );
}

function endSession () {
  if (session) {
    session.terminate();
  }
    $("#hangup").hide();
    $("#call").show();
}

function setupSession (s) {
  endSession();
  session = s;

  session.on('accepted', onAccepted.bind(session));
  session.once('bye', onTerminated.bind(session));
  session.once('failed', onTerminated.bind(session));
  session.once('cancel', onTerminated.bind(session));
}*/

function onTerminated () {
  session = null;
  attachMediaStream($$('remote-media'), null);

    $("#hangup").hide();
    $("#call").show();
}

function onAccepted () {
  this.mediaHandler.getRemoteStreams().forEach(
    attachMediaStream.bind(null, $$('remote-media'))
  );

    $("#hangup").show();
    $("#call").hide();
}

function attachMediaStream (element, stream) {
  if (typeof element.src !== 'undefined') {
    URL.revokeObjectURL(element.src);
    element.src = URL.createObjectURL(stream);
  } else if (typeof element.srcObject !== 'undefined'
       || typeof element.mozSrcObject !== 'undefined') {
    element.srcObject = element.mozSrcObject = stream;
  } else {
    console.log('Error attaching stream to element.');
    return false;
  }

  ensureMediaPlaying(element);
  return true;
}

function ensureMediaPlaying (mediaElement) {
  var interval = 100;
  mediaElement.ensurePlayingIntervalId = setInterval(function () {
    if (mediaElement.paused) {
      mediaElement.play()
    }
    else {
      clearInterval(mediaElement.ensurePlayingIntervalId);
    }
  }, interval);
}

	document.onkeyup = function (e) {
		e = e || window.event;
		if (e.keyCode === 13) {
			if ($('.web-phone').hasClass('web-phone-inprogress')) {
																	document.getElementById("wpn").focus();
																	$("#endCall").click();
																}
																else 
																{
																	$('#startCall1').click();
																}
															
			return false;
		}  
	}

	var Reg = false;
	var checkReg = true;
	var IncomingCall = false;
	var InCall = false;
	var AttemptingCall = false;

	function log(msg) {
		var logObj = document.getElementById("log");
		logObj.innerHTML = msg;
	}

////////////////////
	function CheckRegister() {
		if (checkReg == true){
			if (Reg == false)
				log("Проверьте Ваш логин и пароль!");
		}
	}

	/*function GetOptions()	{
			var eventHandlers = {
					  'progress':   function(e){  },
					  'failed':     function(e){  },
					  'started':    function(e){
						  rtcSession = e.sender;
						var remoteVideo = document.getElementById('remoteAudio');
						if(rtcSession.getRemoteStreams().length > 0) {
							remoteVideo.src = window.URL.createObjectURL(rtcSession.getRemoteStreams()[0]);
							remoteVideo.play()
						}
					  },
					  'ended':      function(e){ 
												  document.getElementById('wpn').value = '';
												  log("Вызов завершен"); 
											  }
					};
				
			var options = {
			  'eventHandlers': eventHandlers,
			  'mediaConstraints': {'audio': true, 'video': false},
			};
			return options
		}*/
	function Register(phone){
			var a = ['ws://176.124.137.231:5066', 'ws://176.124.137.232:5066'];
			var dic_ws = [];
			for (i = 0; i < a.length; i++) {
				dic_ws.push({ws_uri: ""+a[i]+"", weight: i+1});
			}
			var configuration = {
				  'ws_servers':         dic_ws,
				  'ws_server_max_reconnection': 2,
				  'ws_server_reconnection_timeout': 2,
				  'connection_recovery_min_interval': 2,
				  'connection_recovery_max_interval': 5,
				  'uri':                'sip:'+$("#arg1").val()+'@sip.globalhome.su',
				  'password':           $("#arg2").val(),
				  'registrar_server':   'sip.globalhome.su',
				  'display_name': $("#arg1").val(),
				  'authorization_user': $("#arg1").val()
				};
			
			var reg = false;
			var coolPhone = new JsSIP.UA(configuration);
			coolPhone.on('registered', function(e){ 	
														reg = true;
														log("Зарегистрирован");
														$('.web-phone').addClass('web-phone-active');
														//document.getElementById("register").value = "Выход";
														document.getElementById("wpn").focus();
										//////////////////////////////////////				
														function CallToTarget(target, MyPhone)
															{
																var views, selfView, remoteView, mediaTypes;
																  //selfView = document.getElementById('selfView');
																  remoteView = document.getElementById('remoteAudio');
																  
																  views = {remoteView: remoteView};
																 MyPhone.start();
																  try {
                                                                    
																	MyPhone.on('newRTCSession', function(e) {
																		GUI.new_session(e);
																	});
																	log("Звоним на " + target); 

																	MyPhone.call(target, {
																	  mediaConstraints: { audio: true, video:false },
																	  RTCOfferConstraints: { mandatory: { OfferToReceiveAudio: false } }
																	});
																	//GUI.setCallSessionStatus(session, 'answered');
																  } catch(e){
                                                                    console.log(e.stack);
																	throw(e);
																	return;
																  }
																}
															
															$('#startCall1').click(function(event) {
																if ($('.web-phone').hasClass('web-phone-active')) {
																	var target = $("#wpn").val();
																	if (target) {
																	  $("#wpn").val("");
																	  CallToTarget(target, coolPhone);
																	}
																}
																});
														
														
									//////////////////////////////					
													}
						);
						
			coolPhone.on('unregistered', function(e){ alert('unregistered'); });
			coolPhone.on('registrationFailed', function(e){log("Проверьте Ваш логин и пароль!");});
			//Reg = true;
			//checkReg = false;
			coolPhone.start();
			coolPhone.on('newRTCSession', function(e) {
				  //alert('newRTCSession');
				  GUI.new_session(e);
				  /*var text = ' is calling you. Accept?';
				  var accept = confirm(text);
				  log("Входящий вызов...");
				  if (accept) {
					alert('accept');
					e.data.session.answer(options);
					$('.web-phone').addClass('web-phone-inprogress');
				  }*/
				  
			  
			});
			return coolPhone;
	}

	function Clear(){
		var str = document.getElementById('wpn').value;
		document.getElementById('wpn').value = str.substring(0,str.length-1);
		document.getElementById('player_symbol').src = /media/+"sounds/snd_delete.wav";
		document.getElementById('player_symbol').play();
	}
	
	/*function Call() {
		alert("call")
	   var configuration = {
		  'ws_servers':         'ws://176.124.137.237:5066',
		  'uri':                'sip:1000007@176.124.137.252:5060',
		  'password':           'jenya1234'
		};
			
		var coolPhone = new JsSIP.UA(configuration);
		// Register callbacks to desired call events
		var eventHandlers = {
		  'progress':   function(e){ /* Your code here  },
		  'failed':     function(e){ /* Your code here  },
		  'started':    function(e){
			rtcSession = e.sender;
			var remoteVideo = document.getElementById('remoteAudio');
			if(rtcSession.getRemoteStreams().length > 0) {
				remoteVideo.src = window.URL.createObjectURL(rtcSession.getRemoteStreams()[0]);
				remoteVideo.play()
			}
		  },
		  'ended':      function(e){ 
									  document.getElementById('wpn').value = '';
									  log("Вызов завершен"); 
								  }
		};
	
		var options = {
		  'eventHandlers': eventHandlers,
		  'mediaConstraints': {'audio': true, 'video': false},
		};
		coolPhone.start();
		log("Звоним на " + $$('wpn').value); 
		coolPhone.call($$('wpn').value, options);
	}*/

	function enter_symbol(symbol)
	{
		document.getElementById('wpn').value = document.getElementById('wpn').value + symbol;
		if (symbol == '*')
			{
				symbol = 'star'
			}
		if (symbol == '#')
			{
				symbol = 'hash'
			}
		document.getElementById("wpn").focus();
		document.getElementById('player_symbol').src = /media/+"sounds/snd_"+symbol+".wav";
		document.getElementById('player_symbol').play();
	}
	
</script>
<audio id="remoteAudio" style="display:block;"></audio>
<div class="web-phone-wrap">
    <div class="web-phone" style="float: left;">
        <div class="web-phone-i">
            <div class="web-phone-number">
                <input type="text" id="wpn" value="89307178077">
            </div>
            <div class="web-phone-status" id="log">Не зарегистрирован</div>
            <div class="web-phone-buttons">
                <i class="web-phone-remove" onclick="Clear()"></i>
                <i class="web-phone-call" id="startCall"></i>
                <i class="web-phone-call" id="startCall1"></i>
                <i class="web-phone-end" id="endCall"></i>
            </div>
            <div class="web-phone-numpad">
                <table>
                    <tr>
                        <td><input class="dtmfclick" type="button" value="1" onclick="enter_symbol('1');"></td>
                        <td><input class="dtmfclick" type="button" value="2" onclick="enter_symbol('2');"></td>
                        <td><input class="dtmfclick" type="button" value="3" onclick="enter_symbol('3');"></td>
                    </tr>
                    <tr>
                        <td><input class="dtmfclick" type="button" value="4" onclick="enter_symbol('4');"></td>
                        <td><input class="dtmfclick" type="button" value="5" onclick="enter_symbol('5');"></td>
                        <td><input class="dtmfclick" type="button" value="6" onclick="enter_symbol('6');"></td>
                    </tr>
                    <tr>
                        <td><input class="dtmfclick" type="button" value="7" onclick="enter_symbol('7');"></td>
                        <td><input class="dtmfclick" type="button" value="8" onclick="enter_symbol('8');"></td>
                        <td><input class="dtmfclick" type="button" value="9" onclick="enter_symbol('9');"></td>
                    </tr>
                    <tr>
                        <td><input class="dtmfclick" type="button" value="*" onclick="enter_symbol('*');"></td>
                        <td><input class="dtmfclick" type="button" value="0" onclick="enter_symbol('0');"></td>
                        <td><input class="dtmfclick" type="button" value="#" onclick="enter_symbol('#');"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="web-phone-f">
            <div class="web-phone-f-item">
                <label>
                    <span class="label">Логин</span>
                    <input id="arg1" type="text" class="text" value="4005739">
                </label>
            </div>
            <div class="web-phone-f-item">
                <label>
                    <span class="label">Пароль</span>
                    <input id="arg2" type="password" class="text" value="president">
                </label>
            </div>
            <!--div class="web-phone-f-item web-phone-f-remember">
                <label>
                    <input type="checkbox">
                    <span class="label">Запомнить</span>
                </label>
            </div-->
            <div class="web-phone-f-item web-phone-f-submit">
                <input type="button" id='register' value="Регистрация" onclick="Register('phone1')">
            </div>
        </div>
    </div><!--web-phone-->
</div>
<audio id="player"></audio>
<audio id="player_symbol"></audio>
</body>
</html>