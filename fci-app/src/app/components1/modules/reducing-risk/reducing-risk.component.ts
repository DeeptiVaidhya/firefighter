import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
	selector: 'app-reducing-risk',
	templateUrl: './reducing-risk.component.html',
	styleUrls: ['./reducing-risk.component.css']
})
export class ReducingRiskComponent implements OnInit {

	module = 'Module 1';
	index: any = '1';

	constructor(public route: ActivatedRoute, public router: Router, ) {
		this.module = this.route.snapshot.paramMap.get("module");
		this.index = this.route.snapshot.paramMap.get("index");

		if (this.index == undefined || this.index == '') {
			this.index = '1';
		}
	}

	ngOnInit() {
		this.router.routeReuseStrategy.shouldReuseRoute = () => {
			// do your task for before route
			return false;
		}
	}

}
