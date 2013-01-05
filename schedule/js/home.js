jCube.Include('Array.contains');
jCube.Include('Array.each');
jCube.Include('Element.addClass');
jCube.Include('Element.getElementsBySelector');
jCube.Include('Element.getFirstChild');
jCube.Include('Element.getLastChild');
jCube.Include('Element.injectAfter');
jCube.Include('Element.setHTML');
jCube.Include('Element.setStyle');
jCube.Include('Number.format');
jCube.Include('Number.toTime');
jCube.Include('String.toInteger');
jCube.Include('Number.toDate');
jCube.Include('Window.DOMReady');

jCube(function(){
	var config	= {
		dayWidth: 20
	}
	var totalDays	= 0;
	var nearestDay	= (function() {//get the nearest day index
		var x, t;
		var nearestDay	= [Number.MAX_VALUE, null];
		var furthest	= new Date();
		var lastDate	= new Date();
		data.each(function( o, i){
			if ( this.schedule) {
				x	= this.schedule.split('/');
				this.tschedule	= new Date( Number(x[0]), Number(x[1])-1, Number(x[2]));
			} else {
				this.tschedule	= new Date();
				this.tschedule.setTime( lastDate.getTime());
				
			}
			this.incrementedDays	= 0;
			//tmpDate e lastDate destinam-se a automatizar o processo de encontrar a duração ideal para os eventos
			var tmpDate	= new Date();
			tmpDate.setTime(lastDate.getTime());
			for ( var j=0; j<this.duration; j++) {
				tmpDate.setDate( tmpDate.getDate()+1);
				if ( tmpDate.getDay()==6 || tmpDate.getDay()==0 || holydays.contains(tmpDate.getTime().toDate('%Y/%m/%d'))) {
					//tmpDate.setDate(tmpDate.getDate()+1);
					if ( !this.schedule) {
						this.duration++;
						this.incrementedDays++;
					}
				}
			}
			if ( !this.ignoreSchedule) {
				lastDate	= tmpDate;
			}
			this.schedule	= this.schedule? this.schedule: this.tschedule.getTime().toDate('%Y/%m/%d');
			
			
			t	= new Date();
			t.setTime(this.tschedule.getTime());
			if ( t.getTime() < nearestDay[0] ) {
				nearestDay	= [ t.getTime(), i];
			}
			if ( t.getTime()+86400000*this.duration > furthest.getTime()) {
				furthest.setTime(t.getTime());
				furthest.setDate(t.getDate()+this.duration);
			}
		});
		t.setTime(nearestDay[0]);
		totalDays	= (furthest.getTime()-t.getTime()).toTime('%d').toInteger() + 2;
		
		return t;
	})();
	var nearestDayCopy	= new Date( nearestDay.getFullYear(), nearestDay.getMonth(), nearestDay.getDate());
	(function() {//build the calendar months and days
		//start from the first day
		var eMonths	= jCube(':.Scheduler .stage > .months');
		var eDays	= jCube(':.Scheduler .stage > .days');
		var crrMonth	= nearestDay.getMonth()-1;
		var startDay	= nearestDay.getDate();
		var crrDay	= startDay;
		var monthW	= 0;
		var htmlDays	= ['<div class="column" >'];
		var htmlDays2	= ['<div class="label" >'];
		var today	= new Date();today	= today.getFullYear() +'/'+ today.getMonth() +'/'+ today.getDate();
		var isHolyday	= false;
		for ( var i=0, t=nearestDay, div=document.createElement('DIV'), className='', isToday='', isPrevious='previous'; i<totalDays; i++) {
			//create month
			if ( crrMonth != t.getMonth()) {
				if ( eMonths.getLastChild()) {
					eMonths.getLastChild().setStyle('width', monthW);
				}
				monthW	= 0;
				crrMonth	= t.getMonth();
				eMonths.appendChild( jCube(document.createElement('DIV')).setHTML( jCube.Date.pt.months[crrMonth]));
			}
			monthW	+= config.dayWidth;
			
			//holyDay
			isHolyday	= holydays.contains((t.getTime().toDate('%Y/%m/%d')))? 'holyday': '';
			//create day
			crrDay++;
			isToday	= (t.getFullYear() +'/'+ t.getMonth() +'/'+ t.getDate())==today? 'today': '';
			if ( t.getDay() == 6) {
				className	= ' class="saturday '+ isToday +' '+ isPrevious +' '+ isHolyday +'" ';
			} else if ( t.getDay() == 0) {
				className	= ' class="sunday '+ isToday +' '+ isPrevious +' '+ isHolyday +'" ';
			} else {
				className	= ' class="'+ isToday +' '+ isPrevious +' '+ isHolyday +'" ';
			}
			
			htmlDays.push('<div'+className+'></div>');
			htmlDays2.push('<div'+className+'>'+ t.getDate() +'</div>');
			t.setDate(t.getDate()+1);
			
			if ( isToday) {
				isPrevious	= '';
			}
		}
		htmlDays.push('<div class="clearfix" ></div></div>');
		htmlDays2.push('<div class="clearfix" ></div></div>');
		eDays.innerHTML	= htmlDays.join('') + htmlDays2.join('');
		
		//aplique a largura no último mês, visto que o último não teve a largura definida
		if ( jCube(':.Scheduler .stage .months').getLastChild()) {
			jCube(':.Scheduler .stage .months').getLastChild().setStyle('width', monthW);
		}
		
	})();
	
	
	var ePanel	= jCube(':.Scheduler .panel');
	var eEvents	= jCube(':.Scheduler .stage > .schedule');
	var htmlEvents	= [];
	var today	= new Date();
	for ( var i=0, crr=null, div=null, l=0, left, percent=0, className='', tfinish; i<data.length; i++) {
		crr	= data[i];
		
		//color
		className	= crr.className? ' class="'+ crr.className +'"': '';
		
		//create events
		l	= (crr.tschedule.getTime()-nearestDayCopy.getTime()).toTime('%e').toInteger() * config.dayWidth;	//days past * dayW
		w	= crr.duration * config.dayWidth;
		
		//percent indicating progress done
		percent	= 0;
		left	= 0;
		if ( crr.start) {
			percent	= crr.start.split('/');
			percent	= new Date(Number(percent[0]), Number(percent[1])-1, Number(percent[2]));
			
			tfinish	= (crr.finish || (today.getFullYear() +'/'+ (today.getMonth()+1) +'/'+ today.getDate())).split('/');
			tfinish	= new Date(Number(tfinish[0]), Number(tfinish[1])-1, Number(tfinish[2])+1);
			tfinish.setTime( Math.max(tfinish.getTime(), percent.getTime()));
			
			left	= (percent.getTime()-nearestDayCopy.getTime()).toTime('%e').toInteger() * config.dayWidth;	//days past * dayW
			left	= (left-l);
			
			percent	= (tfinish.getTime() - percent.getTime()).toTime('%e').toInteger() * config.dayWidth;
		}
		
		div	= document.createElement('DIV');
		div.innerHTML	= '<div class="row '+ crr.className +'" style="opacity:'+ (crr.finish? 0.2: 1-crr.done+0.3) +';" ><div class="task" >'+ crr.task +'</div><div class="done" >'+ (crr.finish?'100': Math.round(crr.done*100)) +'%</div><div class="duration" >'+ (crr.duration-crr.incrementedDays) +'</div></div>';
		ePanel.appendChild( div.firstChild);
		
		htmlEvents.push('<div><div '+ className +' style="left:'+ l +'px; width:'+ w +'px;" title="'+ crr.task +'. Previsão de início: '+ crr.tschedule.getTime().toDate('%W, %d de %M') +'. Duração: '+ (crr.duration-crr.incrementedDays) +' dia'+ ((crr.duration-crr.incrementedDays)>1?'s':'') +'" ><div'+ className +' style="left:'+ left +'px; width:'+ percent +'px;"' +' ></div></div></div>');
	}
	ePanel.appendChild( jCube(document.createElement('DIV')).addClass('clearfix'));
	eEvents.innerHTML	= htmlEvents.join('');
	
	(function(){//total progress done
		var childs	= jCube('::.Scheduler .panel > .row .done');
		var eDays	= jCube('::.Scheduler .panel > .row .duration');
		var done	= 0;
		var total	= 0;
		var days	= 0;
		
		for ( var i=0; i<childs.length; i++) {
			done	+= childs[i].innerHTML.toInteger();
			total	+= 100;
			days	+= Number(eDays[i].innerHTML);
		}
		
		jCube(document.createElement('DIV')).injectAfter( ePanel.getFirstChild()).setHTML('<div class="row bg-neutral" ><div class="task" >Desenvolvimento total</div><div class="done" >'+ ((done/total)*100).format(0) +'%</div><div class="duration" >'+ days +'</div></div>');
	})();
	
	//sizes
	//height
	jCube(':.Scheduler .stage > .days > .column').setStyle('height', jCube(':.Scheduler .body .panel').offsetHeight - jCube(':.Scheduler .body .panel .row-top').offsetHeight + jCube(':.Scheduler .stage .days').offsetTop);
	//width
	var W	= jCube(':.Scheduler .stage').offsetLeft;
	jCube('::.Scheduler .stage .months > div').each(function(){
		W	+= this.offsetWidth + 2;//adicione a borda, por segurança
	});
	jCube(':.Scheduler > .body').setStyle('width', W * 2 - jCube(':.Scheduler .panel').offsetWidth + 40);//adicone mais dois dias, por segurança
	jCube('::.Scheduler .stage > .days > .column').setStyle('width', W+config.dayWidth*2);
	window.onscroll	= function() {
		jCube(':.Scheduler .panel').setStyle('left', window.scrollX)
		jCube(':.Scheduler .stage > .months').setStyle('top', window.scrollY)
		jCube(':.Scheduler .stage .days .label').setStyle('top', window.scrollY);
	}
});
