import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
	selector: 'app-scope-of-problem',
	templateUrl: './scope-of-problem.component.html',
	styleUrls: ['./scope-of-problem.component.css']
})
export class ScopeOfProblemComponent implements OnInit {
	iframe_html: any;
	index: any = 0;
	module = 'module-1';
	title = '';
	scopeId = '';

	public constructor(public route: ActivatedRoute, public router: Router
	) {
		this.module = route.snapshot.paramMap.get("module");
		this.index = route.snapshot.paramMap.get("index");
		this.scopeId = this.module.trim() + '-' + this.index.trim();

		console.log(this.scopeId);
		if (this.index == undefined || this.index == '') {
			this.index = 0;
		}
	}

	ngOnInit() {
	}

}
