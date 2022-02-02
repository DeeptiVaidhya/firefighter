import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { QuestionnaireService } from '../../service/questionnaire.service';
import { ToastrService } from 'ngx-toastr';
import { DataService } from '../../service/data.service';
import { HttpClient } from '@angular/common/http';
import { HelperService } from '../../service/helper.service';

@Component({
	selector: 'app-resources',
	templateUrl: './resources.component.html',
	styleUrls: ['./resources.component.css'],
})
export class ResourcesComponent implements OnInit {
	breadcrumb = [{ link: '/', title: 'Home' }, { title: 'External Resources', class: 'active' }];
	resources: any;
	content_name: string = "";

	description: string = "";
	id: Number = null;
	link: string = "";
	title: string = "";
	type: string = "";
	callee_page: string = "";
	modalIsShown: boolean = false;
	contentId: any;
	resourceDetail: any;
	resourceId: any;

	constructor(
		public route: ActivatedRoute,
		public questService: QuestionnaireService,
		public toastr: ToastrService,
		public helper: HelperService
	) {
		this.getContent();
	}

	ngOnInit() { }

	getContent() {
		this.questService.get_resources().subscribe(response => {
			if (response["status"] == "success") {
				this.resources = response['data'];
			}
		});
	}

	openModal(resource, contentId?: any) {
		this.modalIsShown = !this.modalIsShown;
		this.callee_page = resource.title;

		if (contentId) {
			this.contentId = contentId;
		}
		this.resourceDetail = resource;
	}

	videoTimeUpdated(resource_id) {
		this.resourceId = resource_id;
	}

	goToSite(resource: any,id) {
		this.addResourceVisited({
			contentId: id,
			callee_page: resource.title,
			resource_id: resource.id
		});
		window.open(resource.link, "_blank");
	}

	modalClosed() {
		this.modalIsShown = false;
	}

	addResourceVisited(content) {
		console.log('Thiss',content);
		
		this.questService
			.addResourceVisited({
				content_id: content.contentId,
				callee_page: content.callee_page,
				resource_id: content.resource_id
			})
			.subscribe(response => {
				if (response["status"] == "success") {
				}
			});
	}
}
