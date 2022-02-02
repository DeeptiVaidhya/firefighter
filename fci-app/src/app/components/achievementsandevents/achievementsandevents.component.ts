import { Component, OnInit } from "@angular/core";

@Component({
	selector: "app-achievementsandevents",
	templateUrl: "./achievementsandevents.component.html",
	styleUrls: ["./achievementsandevents.component.css"]
})
export class AchievementsandeventsComponent implements OnInit {
	// userDetail: any;
	// currentWeek: any;
	// totalTimeSpentOnSite: any;
	// totalWatchedAudioVideo: any;
	// weekInfo: any;
	// armAlloted: string;
	constructor(
		// private questService: QuestionnaireService,
		// public helper: HelperService
	) { }

	ngOnInit() {
		// this.questService.patients_weekly_questionnaire().subscribe(
		// 	result => {
		// 		const response = result;
		// 		this.userDetail =
		// 			response["data"] &&
		// 			response["data"].hasOwnProperty("user_detail") &&
		// 			response["data"].user_detail;
		// 		this.currentWeek = response["data"].week_number;
		// 		this.weekInfo = response["data"].week_info;
		// 		this.armAlloted = response["arm"].toUpperCase();
		// 	},
		// 	err => {
		// 		console.log(err);
		// 	}
		// );
	}

	// showAward(week: any, type: string, i: any) {
	// 	return this.helper.showAward(week, type, i, this.armAlloted);
	// }

	// getWeekEvent(text: string) {
	// 	return text ? text.replace(/\r\n|\n/g, "<br/>") : '';
	// }
}
