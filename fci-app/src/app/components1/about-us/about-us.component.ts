import { Component, OnInit } from '@angular/core';

@Component({
	selector: 'app-about-us',
	templateUrl: './about-us.component.html',
	styleUrls: ['./about-us.component.css'],
})
export class AboutUsComponent implements OnInit {
	breadcrumb = [{ link: '/', title: 'Home' }, { title: 'About Us', class: 'active' }];

	constructor() { }

	ngOnInit() { }
}
