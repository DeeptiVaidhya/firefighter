import { Component } from '@angular/core';
import { FormArray, FormBuilder, FormGroup } from '@angular/forms';
import { ActivatedRoute, Router } from "@angular/router";
import { ToastrService } from "ngx-toastr";
import { CONSTANTS } from '../../config/constants';
import { DataService } from "../../service/data.service";
import { HelperService } from '../../service/helper.service';
import { QuestionnaireService } from "../../service/questionnaire.service";

@Component({
	selector: 'app-exercise',
	templateUrl: './exercise.component.html',
	styleUrls: ['./exercise.component.css']
})

export class ExerciseComponent {
	public isLoggedIn: Boolean = true;
	exerciseForm: FormGroup;
	formArray: FormArray
	timedOut = false;
	isHeaderHidden = false;
	slug: string = "";
	chapter: string = "";
	pageContent: any;
	interval: any;
	breadcrumb = [{ link: "/patient/dashboard", title: "Home" }];
	contentId: any;
	exerciseId: any;
	currentExerciseId: any;
	next: any;
	pre: any;
	url: string = "";
	exercise_items: {}[];
	answers: any;

	constructor(
		private formBuilder: FormBuilder,
		public route: ActivatedRoute,
		public questService: QuestionnaireService,
		public toastr: ToastrService,
		private router: Router,
		private dataService: DataService,
		public helper: HelperService
	) {
		this.getContent();
		this.answers = [];
		this.exerciseForm = this.formBuilder.group({
			target: new FormArray([]),
		});

	}

	ngOnInit() {
	}

	// convenience getters for easy access to form fields
	get f() { return this.exerciseForm.controls; }
	get t() { return this.f.target as FormArray; }

	clearFormArray() {
		while (this.t.length > 0) {
			this.t.removeAt(0);
		}
	}

	getContent() {
		this.exercise_items = [];
		this.route.queryParams.subscribe(param => {
			this.contentId = param.content_id;
			this.exerciseId = this.currentExerciseId = param.id;
			this.slug = param.slug;
			this.getExerciseDetail();
		});
	}

	getExerciseDetail() {
		this.breadcrumb = [{ link: "/patient/dashboard", title: 'Home' }, { link: "/patient/dashboard/" + this.slug, title: this.slug },];

		this.navigateToElem();

		this.questService.exerciseDetails({
			content_id: this.contentId,
			exercise_id: this.exerciseId,
			fields_all: true
		}).subscribe(response => {
			let obj: any;
			if (response["status"] == "success") {
				this.pageContent = response['data']['exercise'][0];
				this.exercise_items = this.findBlankItems(response['data']['exercise_items']);
				this.next = response['data']['next'];
				this.pre = response['data']['pre'];
				this.currentExerciseId = this.exerciseId;

				this.url = CONSTANTS.API_ENDPOINT + 'export/download-worksheet?token=' + this.helper.getToken() + '&fid=';

				obj = { link: "", title: this.pageContent.title, class: "active" };
				this.breadcrumb.push(obj);
			} else if (response["status"] == "error") {
				this.toastr.error(response["msg"]);
			}
		});
	}

	optionFrmCtrlName(length, type1 = '') {
		let target = { [type1]: ['', ''] };
		if (length == null || length == undefined) {
			return;
		} else if (length == 0) {
			this.t.push(this.formBuilder.group(target));
		} else if (length > 0) {
			for (let key = 0; key < length; key++) {
				this.t.push(this.formBuilder.group(target));
			}
		}
	}


	findBlankItems(exerciseItems) {
		let arr = exerciseItems.filter(item => {
			return item.type != 'GOAL_TRACKING' ? (item.primary_prompt ? true : item.secondary_prompt ? true : item.first_heading ? true : item.second_heading ? true : false) : true;
		});

		return arr;
	}

	goToElem(obj) {
		this.router.navigate(['/patient/dashboard/']).then(() => {
			this.dataService.changeMessage(obj);
		}); //'/patient/dashboard/understanding-breast-cancer'
	}

	navigateToElem() {
		this.dataService.currentMessage.subscribe(param => {
			window.scrollTo({
				top: 0,
				left: 0,
				behavior: "smooth"
			});
		});
	}

	updateExercise(id: any, type: string) {
		if (id === 'submit') {
			this.clearFormArray();
			this.submitExercise();
		} else if (id !== 'end') {
			this.exerciseId = id;
		} else if (id == 'end' && type == 'NEXT') {
			this.submitExercise();
			this.router.navigateByUrl('patient/dashboard');
		} else {
			console.log('Content is not found!');
		}
	}

	submitExercise() {  //for submiting question answer
		this.answers = [];
		this.answers.length = 0;

		if (document.querySelectorAll("input[id^='option_gl']").length == 0 || this.checkGoal()) {
			const option = document.querySelectorAll("input[id^='option']:checked");
			this.getAnswer(option);
			const glText = document.querySelectorAll("input[id^='gl_text']");
			this.getAnswer(glText);
			const textItem = document.querySelectorAll("textarea[name^='text_item']");
			this.getAnswer(textItem);
			const twoColFirst = document.querySelectorAll("input[id^='first_two_col']");
			const twoColSecond = document.querySelectorAll("input[id^='second_two_col']");
			this.getAnswer(twoColFirst, twoColSecond);
			const ratingAdmin = document.querySelectorAll("select[id^='rating_admin']");
			this.getAnswer(ratingAdmin, null, true);
			const ratingOther = document.querySelectorAll("input[id^='rating_first']");
			const ratingOther2 = document.querySelectorAll("select[id^='rating_second']");
			this.getAnswer(ratingOther, ratingOther2);
			const goalTck = document.querySelectorAll("input[id^='glt_text']");
			this.getAnswer(goalTck);

		
			this.questService.exerciseDetails({
				content_id: this.contentId,
				exercise_id: this.currentExerciseId,
				exercise_data: this.answers
			}).subscribe(response => {
				if (response.status == 'success') {
					this.getExerciseDetail();
				}
			})

		} else {
			this.toastr.error('Please select 2 or 3 goals');
		}
	}


	checkGoal() {
		let totalGoal = document.querySelectorAll("input[id^='option_gl']:checked").length, textGoal = document.querySelectorAll("input[id^='gl_text']");

		textGoal.forEach(ele => {
			totalGoal += (ele as HTMLInputElement).value.trim() ? 1 : 0;
		});

		return totalGoal > 0;
	}

	getAnswer(obj1, obj2 = null, strR2 = false) {
		if (!this.helper.isEmptyArr(obj1)) {
			for (let i = 0, len = obj1.length; i < len; i++) {

				let response1 = null, response2 = null;
				if (!strR2) {
					response1 = obj1[i]["value"];
					response2 = obj2 ? obj2[i]["value"] : null;
				} else if (strR2) {
					response1 = null;
					response2 = obj1[i]["value"];
				}

				const exerciseItemId = obj1[i].getAttribute("exercise-item-id");
				const exerciseItemDetailsId = obj1[i].getAttribute("exercise-item-details-id");
				const goalId = obj1[i].getAttribute("goal-id");
				const type = obj1[i].getAttribute("type-item");

				this.answers.push({
					exercise_item_id: exerciseItemId,
					exercise_item_details_id: exerciseItemDetailsId,
					goals_id: goalId,
					type: type,
					response_1: response1,
					response_2: response2
				});
			}
		}
	}

	repeatArray(n) {
		if (n && n >= 0) {
			let repeat = parseInt(n)
			return Array(repeat);
		} else {
			return Array(1);
		}

	}

	getGoalTrack(userAnswer: any, prtInd = 1, childInd = 0) {
		if (userAnswer && userAnswer.length > 0) {
			let max = (userAnswer.length / 2), i = prtInd == 1 ? childInd : max + childInd;
			if (max > childInd) {
				return userAnswer[i]['response_1'];
			}
			return '';
		}
		return '';
	}

	ngOnDestroy() {
		this.dataService.changeMessage(null);
	}
}
