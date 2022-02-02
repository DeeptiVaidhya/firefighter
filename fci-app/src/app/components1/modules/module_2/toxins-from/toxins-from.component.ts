import { Component, OnInit } from '@angular/core';

@Component({
	selector: 'app-toxins-from',
	templateUrl: './toxins-from.component.html',
	styleUrls: ['./toxins-from.component.css']
})
export class ToxinsFromComponent implements OnInit {
	normalToxin=true;
	tvClicked=false;
	couchClicked=false;

	constructor() { }

	ngOnInit() { }
	areaClicked(type) {
		this.normalToxin=false;
		this.tvClicked = type=='TV';
		this.couchClicked = type=='COUCH';
	}
	closePopOver($event){
		this.normalToxin=true;
		this.tvClicked=false;
		this.couchClicked=false;
		$event.stopPropagation();
	}
	

}
