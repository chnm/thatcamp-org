/*******************************************************************************\
Countdown Timer JavaScript Module
Version 2.4.3 (kept in step with fergcorp_countdownTimer.php)
Copyright (c) 2007-2010 Andrew Ferguson
---------------------------------------------------------------------------------
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
\*******************************************************************************/

function rtrim(stringToTrim) {
	return stringToTrim.replace(/..$/,"");
}

function _n(singular, plural, count){
	if(count == 1){
		return singular;
	}
	else{
		return plural;
	}
}

function fergcorp_countdownTimer_js ()
{
    for (var i=0; i < fergcorp_countdownTimer_js_events.length; i++) {
		
		var nowDate = new Date();
		var targetDate = new Date(fergcorp_countdownTimer_js_events[i]["targetDate"]*1000);
		//alert(document.getElementById(fergcorp_countdownTimer_js_events[i]["id"]).innerHTML);
		if((targetDate - nowDate) < 0){
			document.getElementById(fergcorp_countdownTimer_js_events[i]["id"]).innerHTML = sprintf(fergcorp_countdownTimer_js_language['ago'], fergcorp_countdownTimer_fuzzyDate(nowDate, targetDate, getOptions));			
		}
		else if((targetDate - nowDate) >= 0 ){
			document.getElementById(fergcorp_countdownTimer_js_events[i]["id"]).innerHTML = sprintf(fergcorp_countdownTimer_js_language['in'],fergcorp_countdownTimer_fuzzyDate(targetDate, nowDate, getOptions));
		}
	}
	
    window.setTimeout('fergcorp_countdownTimer_js()', 1000);
}

function fergcorp_countdownTimer_fuzzyDate(targetTime, nowTime, getOptions){
	var rollover = 0;
	var sigNumHit = false;
	var totalTime = 0;

	var nowDate = nowTime;
	var targetDate = targetTime;
	
	var s = '';
	
	var nowYear = nowDate.getFullYear();
	var nowMonth = nowDate.getMonth() + 1;
	var nowDay = nowDate.getDate();
	var nowHour = nowDate.getHours();
	var nowMinute = nowDate.getMinutes();
	var nowSecond = nowDate.getSeconds();
	
	var targetYear = targetDate.getFullYear();
	var targetMonth = targetDate.getMonth() + 1;
	var targetDay = targetDate.getDate();
	var targetHour = targetDate.getHours();
	var targetMinute = targetDate.getMinutes();
	var targetSecond = targetDate.getSeconds();
	
	var resultantYear = targetYear - nowYear;
	var resultantMonth = targetMonth - nowMonth;
	var resultantDay = targetDay - nowDay;
	var resultantHour = targetHour - nowHour;
	var resultantMinute = targetMinute - nowMinute;
	var resultantSecond = targetSecond - nowSecond;

	if(resultantSecond < 0){
		resultantMinute--;
		resultantSecond = 60 + resultantSecond;
	}
	
	if(resultantMinute < 0){
		resultantHour--;
		resultantMinute = 60 + resultantMinute;
	}
	
	if(resultantHour < 0){
		resultantDay--;
		resultantHour = 24 + resultantHour;
	}
	
	if(resultantDay < 0){
		resultantMonth--;
		resultantDay = resultantDay + 32 - new Date(nowYear, nowMonth-1, 32).getDate();
	}
	
	

	if(resultantMonth < 0){
		resultantYear--;
		resultantMonth = resultantMonth + 12;
	}

	//Year
	if(getOptions['showYear']){
		if(sigNumHit || !getOptions['stripZero'] || resultantYear){
			s = '<span class="fergcorp_countdownTimer_year fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdownTimer_js_language['year'], fergcorp_countdownTimer_js_language['years'], resultantYear), resultantYear) + '</span> ';
			sigNumHit = true;
		}
	}
	else{
		rollover = resultantYear*31536000;
	}

	//Month	
	if(getOptions['showMonth']){
		if(sigNumHit || !getOptions['stripZero'] || (resultantMonth + parseInt(rollover/2628000)) ){
			resultantMonth = resultantMonth + parseInt(rollover/2628000);
			s = s + '<span class="fergcorp_countdownTimer_month fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdownTimer_js_language['month'], fergcorp_countdownTimer_js_language['months'], resultantMonth), resultantMonth) + '</span> ';
			rollover = rollover - parseInt(rollover/2628000)*2628000;
			sigNumHit = true;
		}
	}
	else{
		//If we don't want to show months, let's just calculate the exact number of seconds left since all other units of time are fixed (i.e. months are not a fixed unit of time)		
		totalTime = parseInt(targetTime.getTime() - nowTime.getTime())/1000;
		
		//If we showed years, but not months, we need to account for those.
		if(getOptions['showYear']){
			totalTime = totalTime - resultantYear*31536000;
		}
			
		//Re calculate the resultant times
		resultantWeek = 0;//parseInt( totalTime/(86400*7) );
 
		resultantDay = parseInt( totalTime/86400 );

		resultantHour = parseInt( (totalTime - resultantDay*86400)/3600 );
		
		resultantMinute = parseInt( (totalTime - resultantDay*86400 - resultantHour*3600)/60 );
		
		resultantSecond = parseInt( (totalTime - resultantDay*86400 - resultantHour*3600 - resultantMinute*60) );
		
		//and clear any rollover time
		rollover = 0;

	}
	
	//Week (weeks are counted differently becuase we can just take 7 days and call it a week...so we do that)
	if(getOptions['showWeek']){
		if(sigNumHit || !getOptions['stripZero'] || parseInt( (resultantDay + parseInt(rollover/86400) )/7)){
			resultantDay = resultantDay + parseInt(rollover/86400);
			s = s + '<span class="fergcorp_countdownTimer_week fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdownTimer_js_language['week'], fergcorp_countdownTimer_js_language['weeks'], (parseInt( (resultantDay + parseInt(rollover/86400) )/7))), (parseInt( (resultantDay + parseInt(rollover/86400) )/7))) + '</span> ';
			rollover = rollover - parseInt(rollover/86400)*86400;
			resultantDay = resultantDay - parseInt( (resultantDay + parseInt(rollover/86400) )/7 )*7;
			sigNumHit = true;
		}
	}

	//Day
	if(getOptions['showDay']){
		if(sigNumHit || !getOptions['stripZero'] || (resultantDay + parseInt(rollover/86400)) ){
			resultantDay = resultantDay + parseInt(rollover/86400);
			s = s + '<span class="fergcorp_countdownTimer_day fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdownTimer_js_language['day'], fergcorp_countdownTimer_js_language['days'], resultantDay), resultantDay) + '</span> ';
			rollover = rollover - parseInt(rollover/86400)*86400;
			sigNumHit = true;
		}
	}
	else{
		rollover = rollover + resultantDay*86400;
	}
	
	//Hour
	if(getOptions['showHour']){
		if(sigNumHit || !getOptions['stripZero'] || (resultantHour + parseInt(rollover/3600)) ){
			resultantHour = resultantHour + parseInt(rollover/3600);
			s = s + '<span class="fergcorp_countdownTimer_hour fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdownTimer_js_language['hour'], fergcorp_countdownTimer_js_language['hours'], resultantHour), resultantHour) + '<span> ';
			rollover = rollover - parseInt(rollover/3600)*3600;
			sigNumHit = true;
		}
	}
	else{
		rollover = rollover + resultantHour*3600;
	}
	
	//Minute
	if(getOptions['showMinute']){
		if(sigNumHit || !getOptions['stripZero'] || (resultantMinute + parseInt(rollover/60)) ){
			resultantMinute = resultantMinute + parseInt(rollover/60);
			s = s + '<span class="fergcorp_countdownTimer_minute fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdownTimer_js_language['minute'], fergcorp_countdownTimer_js_language['minutes'], resultantMinute), resultantMinute) + '</span> ';
			rollover = rollover - parseInt(rollover/60)*60;
			sigNumHit = true;
		}
	}
	else{
		rollover = rollover + resultantMinute*60;
	}
	
	//Second
	if(getOptions['showSecond']){
		s = s + '<span class="fergcorp_countdownTimer_second fergcorp_countdownTimer_timeUnit">' + sprintf(_n(fergcorp_countdownTimer_js_language['second'], fergcorp_countdownTimer_js_language['seconds'], resultantSecond), resultantSecond) + '</span> ';
	}
	
	
	//Catch blank statements
	if(s==''){
		if(getOptions['showSecond']){
			s = sprintf(fergcorp_countdownTimer_js_language['seconds'], 0);
		}
		else if(getOptions['showMinute']){
			s = sprintf(fergcorp_countdownTimer_js_language['minutes'], 0);
		}
		else if(getOptions['showHour']){
			s = sprintf(fergcorp_countdownTimer_js_language['hours'], 0);
		}	
		else if(getOptions['showDay']){
			s = sprintf(fergcorp_countdownTimer_js_language['days'], 0);
		}
		else if(getOptions['showWeek']){
			s = sprintf(fergcorp_countdownTimer_js_language['weeks'], 0);
		}
		else if(getOptions['showMonth']){
			s = sprintf(fergcorp_countdownTimer_js_language['months'], 0);
		}
		else{
			s = sprintf(fergcorp_countdownTimer_js_language['years'], 0);
		}
	}

	
	return s.replace(/(, ?<\/span> *)$/, "<\/span>"); //...and return the result (a string)
}
