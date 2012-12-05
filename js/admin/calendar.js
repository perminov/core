var currentCalendarName ;

function showCalendar(name, minimal) {
	var id = name+'Calendar';
	var calendarIframe = document.getElementById(id);
	
	if (calendarIframe.style.display=='none') {
		var minimal = minimal.split('-');
		var current = document.getElementById(name+'Input').value.split('-');

		currentCalendarName = name;

		if (current[1] == '00' || current [2] == '00' || current [0] == '0000') {
			current[0] = new Date().getFullYear();
			current[1] = new Date().getMonth()+1;
			current[2] = new Date().getDate();
		}

		minimalDate = new Date(minimal[0], minimal[1] - 1, minimal[2]);
		currentDate = new Date(current[0], current[1] - 1, current[2]);
		
		
		window.frames[id].YAHOO.example.calendar.cal1.pageDate = currentDate;
		window.frames[id].YAHOO.example.calendar.cal1.selectedDates[0] = new Array(current[0], current[1], current[2]);
		window.frames[id].YAHOO.example.calendar.cal1.minDate = minimalDate;
		window.frames[id].YAHOO.example.calendar.cal1.render();			
		calendarIframe.style.display='block';
	}
}

function mySetDate(date) {
	var calendarIframe = document.getElementById(currentCalendarName + 'Calendar');
	var month = date[0].getMonth()+1;
	var day = date[0].getDate();
	if (month < 10) month = ""+ "0" + month;
	if (day < 10) day = ""+ "0" + day;
	var yearStr = new String(date[0].getFullYear());
	document.getElementById(currentCalendarName + 'Input').value = yearStr + '-' + month + '-' + day;
	calendarIframe.style.display='none';
}
