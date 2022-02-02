import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { DataService } from '../../service/data.service';
import { HelperService } from '../../service/helper.service';
import { QuestionnaireService } from '../../service/questionnaire.service';

@Component({
	selector: 'app-dashboard',
	templateUrl: './dashboard.component.html',
	styleUrls: ['./dashboard.component.css'],
})
export class DashboardComponent implements OnInit {
	data: any;
	arm: string = "";
	username: any;
	user_detail: any = [];
	all_week_status = '';
	breadcrumb = [{ link: "/patient/landing", title: "Home" },{ link: "/patient/dashboard", title: "Dashboard" }];
	pageContent: any;
	currentWeek: any;
	interval: any;
	currentWeekInfo: any;
	routeData: any;
	is_enable_questionnaire: any;
	weekCompleted: boolean;

	constructor(
		private router: Router,
		public toastr: ToastrService,
		private questService: QuestionnaireService,
		public helper: HelperService,
		private dataService: DataService

	) { }

	ngOnInit() {
		localStorage.getItem('username') && (this.username = localStorage.getItem('username'));
		this.patient_weekly_questionnaire();
	}


	patient_weekly_questionnaire() {
		this.questService.patients_weekly_questionnaire().subscribe(
			result => {
				const response = result;
				if (response.hasOwnProperty("chapters")) {
					this.pageContent = response["chapters"];
				}
				this.arm = response.hasOwnProperty("arm")
					? response["arm"].toUpperCase()
					: "";
				if (response.hasOwnProperty("data")) {

					this.user_detail = response["data"].hasOwnProperty("user_detail") && response["data"].user_detail;
					this.currentWeekInfo = response["data"].hasOwnProperty("week_info") && response["data"].week_info;
					this.currentWeek = response["data"].week_number;
					//this.is_enable_questionnaire = response["data"].enable_questionnaire
					this.weekCompleted = response['is_week_completed'];

					//this.is_questionnaire_completed = response["data"].is_questionnaire_completed;
					//this.incompleted_questionnaire = response["data"].incompleted_questionnaire;
				}
				
			},
			err => {
				console.log('Error in dashbord', err);
			}
		);
	}


	goToElem(obj) {
		this.router.navigate(['/patient/dashboard/']).then(() => {
			this.dataService.changeMessage(obj);
		}); //'/patient/dashboard/understanding-breast-cancer'
	}

	reportNotFound() {
		this.toastr.error('Genomic profile report not available.', null);
	}

	viewAllAchivement() {
		if (this.currentWeek > 0 || this.weekCompleted) {
			this.router.navigate(["/achievements"]);
		} else {
			this.toastr.error("Week not started yet");
		}
	}

	getFormatedDate(date: string) {
		return new Date(date.replace(/-/g, "/"));
	}

	// showAward(week: any, type: string, i: any) {
	// 	return this.helper.showAward(week, type, i, this.arm);
	// }
}
