import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
	selector: 'app-case-study',
	templateUrl: './case-study.component.html',
	styleUrls: ['./case-study.component.css']
})
export class CaseStudyComponent implements OnInit {

	module = 'module-1';
	title = 'Module 1';
	index: any = 1;
	nextButtonText = 'Next';

	public constructor(private route: ActivatedRoute, private router: Router) {
		this.module = route.snapshot.paramMap.get("module");
		this.title = this.module.replace(/[\-]/g, ' ');
		this.index = route.snapshot.paramMap.get("index");
		if (this.title) {
			this.title = this.title.charAt(0).toUpperCase() + this.title.slice(1);
		}
		this.nextButtonText = 'Next';
		if (this.module == 'module-3' && this.index == '3') {
			this.nextButtonText = 'Next Module';
		}
	}

	ngOnInit() {
		this.router.routeReuseStrategy.shouldReuseRoute = () => {
			return false;
		}
	}
}
