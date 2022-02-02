import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
	selector: 'app-introduction',
	templateUrl: './introduction.component.html',
	styleUrls: ['./introduction.component.css']
})
export class IntroductionComponent implements OnInit {

	module = 'module-1';
	title = 'Module 1';
	introModules = {
		'module-1': { title: 'Module 1', text1: 'Cancer in the Fire Service', text2: 'Define the scope of the cancer burden in the fire service' },
		'module-2': { title: 'Module 2', text1: 'Firefighter<br> Occupational Cancer Risk', text2: 'Identify firefighter occupational cancer risks' },
		'module-3': { title: 'Module 3', text1: 'Reducing Cancer<br> Risk in the Fire<br> Service', text2: 'Describe cancer risk reduction behaviors and tools' },
		'module-4': { title: 'Module 4', text1: 'Organizational Level<br> Change in the Fire<br> Service', text2: 'Identify organizational level changes that can help reduce cancer risk' },
		'module-5': { title: 'Module 5', text1: 'Ongoing Learning:<br> Resources', text2: 'Describe resources to help firefighters diagnosed with cancer' },
		// 'module-6': { title: 'Module 5', text1: 'Ongoing Learning:<br> Resources', text2: 'Describe resources to help firefighters diagnosed with cancer' },
	};


	public constructor(private route: ActivatedRoute, private router: Router) {
		this.module = route.snapshot.paramMap.get("module");
		this.title = this.module.replace(/[\-]/g, ' ');
		if (this.title) {
			this.title = this.title.charAt(0).toUpperCase() + this.title.slice(1);
		}

	} 

	ngOnInit() {
		this.router.routeReuseStrategy.shouldReuseRoute = () => {
			return false;
		}
	}

	startModule(type) {
		let url = '', index;
		switch (type) {
			case 'module-1': {
				url = '/patient/module-1/video/1';
				// index = '0';
				break;
			}
			case 'module-2': {
				url = '/patient/module-2/firefighter-exposed/1';
				// index = '9';
				break;
			}
			case 'module-3': {
				url = '/patient/module-3/video/1';
				// index = '21';
				break;
			}
			case 'module-4': {
				url = '/patient/module-4/reduce-risk/1';
				// index = '38';
				break;
			}
			case 'module-5': {
				url = '/patient/module-5/case-study/1';
				// index = '41';
				break;
			}
		}

		console.log(url);

		this.router.navigate([url]).catch((e) => {
			console.log('Url not exist!');
		});

	}

}
