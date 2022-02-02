import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';

@Component({
	selector: 'app-breadcrumb',
	templateUrl: './breadcrumb.component.html',
	styleUrls: ['./breadcrumb.component.css'],
})
export class BreadcrumbComponent implements OnInit {
	@Input('breadcrumb') breadcrumb: any;
	@Input('isFooter') isFooter: boolean=false;
	@Output() onClickTitle = new EventEmitter();

	constructor() {}

	titleClickEvent(params){
		this.onClickTitle.emit(params);
	}
	ngOnInit() {}
}
