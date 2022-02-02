import { Component, Input, OnInit } from '@angular/core';
import { ActivatedRoute, NavigationEnd, Router } from '@angular/router';
import { VgAPI } from "videogular2/core";
import { CONSTANTS } from '../../../config/constants';
import { QuestionnaireService } from "../../../service/questionnaire.service";


@Component({
	selector: 'app-footer',
	templateUrl: './footer.component.html',
	styleUrls: ['./footer.component.css']
})
export class FooterComponent implements OnInit {

	@Input() isModulePage: any = false;
	module = 'module-1';
	
	vgApi:VgAPI;
	isVideoPause=false;
	isVideoMute=0;
	volume=1;
	audioSrc=''; 
	public constructor(private route: ActivatedRoute, 
		public questionnaireService: QuestionnaireService,
		private router: Router) {
		// console.log('footer called cons');
	}

	ngOnInit(){
		this.router.events.subscribe(evt => {
			if (!(evt instanceof NavigationEnd)) {
				return;
			}
			let url = this.router.url.split('/');
			this.module = (url.length && url[2]) || 'module-1';
			
			let sp=this.router.url.split('/patient/');
			let file_name = sp.length && sp[1] && (sp[1].split('/').join('_'))+'.mp3';

			this.audioSrc!=''?this.volume=this.vgApi['volume']:'';
			this.audioSrc='';
			
			this.questionnaireService
            .moduleAudio({ file_name:file_name })
            .subscribe(response => {
                if (response["status"] == "success") {
                	console.log('video pause',this.isVideoPause);
					if(response['status']=='success'){
						this.audioSrc=response['url'];

						setTimeout(function(){
							!this.isVideoPause?this.vgApi.play():this.vgApi.pause();
							this.vgApi.volume=this.volume;
						}.bind(this),500)

					}
                } 
            });
			
		}); 
	}


  	onPause(event){
  		this.isVideoPause=this.vgApi['state']=='playing';
  	}

 	onPlayerReady(api:VgAPI) {
 		console.log('>>fg');
	    this.vgApi = api;
  	} 
}
