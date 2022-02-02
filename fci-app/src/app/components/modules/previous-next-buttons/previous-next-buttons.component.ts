import { Component, Input, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
	selector: 'app-previous-next-buttons',
	templateUrl: './previous-next-buttons.component.html',
	styleUrls: ['./previous-next-buttons.component.css']
})
export class PreviousNextButtonsComponent implements OnInit {

	@Input() nextButtonText = 'Next';
	@Input() isNextButtonDisabled = false;
	title: any;
	routeIndex: any = 0;

	routes = [
		//module 1
		//{ module: '1', back: 'intro', current: 'video/1', next: 'scope-of-problem/1', title: 'Survivorship' },
		{ module: '1', back: 'intro', current: 'video/1', next: 'learning-objectives/1', title: 'Survivorship' },
		{ module: '1', back: 'video/2', current: 'learning-objectives/1', next: 'scope-of-problem/1', },

		{ module: '1', back: 'video/1', current: 'scope-of-problem/1', next: 'meta-analysis' },

		{ module: '1', back: 'scope-of-problem/1', current: 'meta-analysis', next: 'niosh/1' },
		{ module: '1', back: 'meta-analysis', current: 'niosh/1', next: 'scope-of-problem/2' },
		{ module: '1', back: 'niosh/1', current: 'scope-of-problem/2', next: 'after-fighting-fire/1' },
		{ module: '1', back: 'scope-of-problem/2', current: 'after-fighting-fire/1', next: 'firefighter-cancer-risk/1' },
		{ module: '1', back: 'after-fighting-fire/1', current: 'firefighter-cancer-risk/1', next: 'video/2' },
		// { module: '1', back: 'firefighter-cancer-risk/1', current: 'video/2', next: 'learning-objectives/1', title: 'Firefighter Cancer Epidemic' },
		{ module: '1', back: 'firefighter-cancer-risk/1', current: 'video/2', next: 'assessment-evaluation', title: 'Firefighter Cancer Epidemic' },
		// { module: '1', back: 'video/2', current: 'learning-objectives/1', next: 'assessment-evaluation', },
		//{ module: '1', back: 'video/2', current: 'learning-objectives/1', next: 'assessment-evaluation', },

		//module 2
		{ module: '2', back: 'intro', current: 'firefighter-exposed/1', next: 'occupational-exposure/1' },
		{ module: '2', back: 'firefighter-exposed/1', current: 'occupational-exposure/1', next: 'toxins-from/1' },
		{ module: '2', back: 'occupational-exposure/1', current: 'toxins-from/1', next: 'firefighter-exposed/2' },
		{ module: '2', back: 'toxins-from/1', current: 'firefighter-exposed/2', next: 'on-scene/1' },
		{ module: '2', back: 'firefighter-exposed/2', current: 'on-scene/1', next: 'on-scene/2' },
		{ module: '2', back: 'on-scene/1', current: 'on-scene/2', next: 'on-scene/3' },
		{ module: '2', back: 'on-scene/2', current: 'on-scene/3', next: 'on-scene/4' },
		{ module: '2', back: 'on-scene/3', current: 'on-scene/4', next: 'video/1' },
		{ module: '2', back: 'on-scene/4', current: 'video/1', next: 'firefighter-exposed/3', title: 'Bunker Gear Transfer: The Invisible Danger' },
		{ module: '2', back: 'video/1', current: 'firefighter-exposed/3', next: 'video/2' },
		{ module: '2', back: 'firefighter-exposed/3', current: 'video/2', next: 'video/3', title: 'Sleep Disruption in the Fire Service: Dr. Laura Barger' },
		{ module: '2', back: 'video/2', current: 'video/3', next: 'assessment-evaluation', title: 'Nutrition in the Fire Service: Dr. Saar Jahnke' },
		
		//module 3
		{ module: '3', back: 'intro', current: 'video/1', next: 'reduce-risk/1', title: 'Decontamination Research: Dr. Gavin Horn' },
		{ module: '3', back: 'video/1', current: 'reduce-risk/1', next: 'reduce-risk/2' },
		{ module: '3', back: 'reduce-risk/1', current: 'reduce-risk/2', next: 'reduce-risk/3' },
		{ module: '3', back: 'reduce-risk/2', current: 'reduce-risk/3', next: 'reduce-risk/4' },
		{ module: '3', back: 'reduce-risk/3', current: 'reduce-risk/4', next: 'reduce-risk/5' },
		{ module: '3', back: 'reduce-risk/4', current: 'reduce-risk/5', next: 'reduce-risk/6' },
		{ module: '3', back: 'reduce-risk/5', current: 'reduce-risk/6', next: 'reduce-risk/7' },
		{ module: '3', back: 'reduce-risk/6', current: 'reduce-risk/7', next: 'reduce-risk/8' },
		{ module: '3', back: 'reduce-risk/7', current: 'reduce-risk/8', next: 'reduce-risk/9' },
		{ module: '3', back: 'reduce-risk/8', current: 'reduce-risk/9', next: 'reduce-risk/10' },
		{ module: '3', back: 'reduce-risk/9', current: 'reduce-risk/10', next: 'case-study/1' },
		{ module: '3', back: 'reduce-risk/10', current: 'case-study/1', next: 'video/2' },
		{ module: '3', back: 'case-study/1', current: 'video/2', next: 'video/3', title: 'Post Fire On-scene Gross Decontamination' },
		{ module: '3', back: 'video/2', current: 'video/3', next: 'reduce-risk/11', title: 'Gross Decon FAQ Video' },
		{ module: '3', back: 'video/3', current: 'reduce-risk/11', next: 'assessment-evaluation' },
		{ module: '3', back: 'question/2', current: 'case-study/2', next: 'case-study/3' },
		
		{ module: '3', back: 'case-study/2', current: 'case-study/3', next: 'fire-service/0' },
		{ module: '3', back: 'fire-service/0', current: 'fire-service/1', next: 'fire-service/2'},
		{ module: '3', back: 'fire-service/1', current: 'fire-service/2', next: 'fire-service/3',title:"Food Choices / Timing / Speed" },
		{ module: '3', back: 'fire-service/2', current: 'fire-service/3', next: 'fire-service/4'},
		{ module: '3', back: 'fire-service/3', current: 'fire-service/4', next: 'fire-service/5'},
		{ module: '3', back: 'fire-service/4', current: 'fire-service/5', next: 'fire-service/6'},
		{ module: '3', back: 'fire-service/5', current: 'fire-service/6', next: 'fire-service/7'},
		{ module: '3', back: 'fire-service/6', current: 'fire-service/7', next: 'fire-service/8'},
		{ module: '3', back: 'fire-service/7', current: 'fire-service/8', next: 'fire-service/9'},
		{ module: '3', back: 'fire-service/8', current: 'fire-service/9', next: 'fire-service/10'},
		{ module: '3', back: 'fire-service/9', current: 'fire-service/10', next: 'fire-service/11'},
		{ module: '3', back: 'fire-service/10', current: 'fire-service/11', next: 'fire-service/12'},
		{ module: '3', back: 'fire-service/11', current: 'fire-service/12', next: 'fire-service/13'},
		{ module: '3', back: 'fire-service/12', current: 'fire-service/13', next: 'fire-service/14'},
		{ module: '3', back: 'fire-service/13', current: 'fire-service/14', next: 'fire-service/15'},
		{ module: '3', back: 'fire-service/14', current: 'fire-service/15', next: 'fire-service/16'},
		{ module: '3', back: 'fire-service/15', current: 'fire-service/16', next: 'fire-service/17'},
		{ module: '3', back: 'fire-service/16', current: 'fire-service/17', next: 'fire-service/18'},
		{ module: '3', back: 'fire-service/17', current: 'fire-service/18', next: 'fire-service/19'},
		{ module: '3', back: 'fire-service/18', current: 'fire-service/19', next: 'fire-service/20'},
		{ module: '3', back: 'fire-service/19', current: 'fire-service/20', next: 'intro'},

		//module 4
		{ module: '4', back: 'intro', current: 'reduce-risk/1', next: 'reduce-risk/2' },
		{ module: '4', back: 'reduce-risk/1', current: 'reduce-risk/2', next: 'video/1' },
		{ module: '4', back: 'reduce-risk/2', current: 'video/1', next: 'assessment-evaluation', title: 'Florida’s Efforts on Cancer Prevention' },

		//module 5
		{ module: '5', back: 'intro', current: 'case-study/1', next: 'video/1' },
		{ module: '5', back: 'case-study/1', current: 'video/1', next: 'thank-you', title: 'Finding Cancer Resources' },
		{ module: '5', back: 'video/1', current: 'thank-you', next: '' },

	];


	public constructor(public route: ActivatedRoute, public router: Router
	) { }

	ngOnInit() {
		console.log('url',this.router.url);
		let temp = this.getIndex();
		this.routeIndex = temp ? parseInt(temp) : 0;
		this.title = this.routes[this.routeIndex]['title'];
	}

	getIndex() {
		let url = '', index;
		
		index = this.routes.findIndex((route) => {
			url = '/patient/module-' + route['module'] + '/' + route['current']
			console.log('Url>>',url);
			if (this.router.url.trim() == url) {
				return true;
			};
		});

		console.log('index', index);
		return index != -1 ? index : 0;
	}

	navigate(type = "NEXT") {
		let temp = this.getIndex(), next, url, route;
		this.routeIndex = temp ? parseInt(temp) : 0;
		route = this.routes[this.routeIndex];

		if (type == "NEXT") {
			url = '/patient/module-' + route['module'] + '/' + route['next'];
			url = url == '/patient/module-3/intro' ? '/patient/module-4/intro' : url;
		} else {
			url = '/patient/module-' + route['module'] + '/' + route['back'];
		}
		this.router.navigateByUrl(url).catch((e) => {
			console.log('Url not exist!');
		});
	}

}
