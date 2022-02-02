import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-firefighter-exposed',
  templateUrl: './firefighter-exposed.component.html',
  styleUrls: ['./firefighter-exposed.component.css']
})
export class FirefighterExposedComponent implements OnInit {

	module = 'module-1';
	title = 'Module 1';
	index = 'Module 1';

	

	public constructor(private route: ActivatedRoute, private router: Router) {
		this.module = route.snapshot.paramMap.get("module");
		this.index = route.snapshot.paramMap.get("index");
		this.title = this.module.replace(/[\-]/g, ' ');
		if (this.title) {
			this.title = this.title.charAt(0).toUpperCase() + this.title.slice(1);
		}
	}

	ngOnInit() {
	}

}
