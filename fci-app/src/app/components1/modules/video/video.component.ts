import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { EmbedVideoService } from 'ngx-embed-video';

@Component({
	selector: 'app-video',
	templateUrl: './video.component.html',
	styleUrls: ['./video.component.css']
})
export class VideoComponent implements OnInit {
	iframe_html: any;
	videoIndex: any = 1;
	module = 'module-1';
	title = 'Module 1';
	preload: string = 'auto';

	nextUrl: any;
	prevUrl: any;
	videoLink = {
		'module-1-1': '346998513',
		'module-1-2': '347150206',
		'module-2-1': '347150206',
		'module-2-2': '346997816',
		'module-2-3': '346998672',
		'module-3-1': '346999051',
		'module-3-2': '327408824',
		'module-3-3': '336415585',
		'module-4-1': '346998909',
		'module-5-1': '346998045',
	}

	// Slide 2- Survivorship- https://vimeo.com/showcase/6117251/video/346998513
	// Slide 9- FF Cancer Epidemic- https://vimeo.com/showcase/6117251/video/347150206
	// Slide 24- Bunker Gear Transfer- Not ready yet
	// Slide 26- Sleep Disruption- https://vimeo.com/showcase/6117251/video/346997816
	// Slide 27- Nutrition- https://vimeo.com/showcase/6117251/video/346998672
	// Slide 44- Decon Research- https://vimeo.com/showcase/6117251/video/346999051
	// Slide 56- Decon- https://vimeo.com/327408824
	// Slide 57- Decon FAQ- https://vimeo.com/336415585
	// Slide 77- Florida- https://vimeo.com/showcase/6117251/video/346998909
	// Slide 87- FF Cancer Resources- https://vimeo.com/showcase/6117251/video/346998045

	public constructor(public route: ActivatedRoute, public router: Router, public embedService: EmbedVideoService
	) {
		let videoLinkKey;
		this.module = route.snapshot.paramMap.get("module");
		this.videoIndex = parseInt(route.snapshot.paramMap.get("index"));
		console.log('>>', this.videoIndex);
		if (this.videoIndex == undefined || this.videoIndex == '') {
			this.videoIndex = 1;
		}

		videoLinkKey = this.module + '-' + this.videoIndex;
		console.log(this.videoLink[videoLinkKey]);
		if (this.videoLink[videoLinkKey] != undefined) {
			this.iframe_html = this.embedService.embed_vimeo(this.videoLink[videoLinkKey], {
				attr: { width: '100%', height: 550 }
			});
		}

	}

	ngOnInit() {
		this.router.routeReuseStrategy.shouldReuseRoute = () => {
			// do your task for before route
			return false;
		}
	}

}
