import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { QuestionnaireService } from 'src/app/service/questionnaire.service';


@Component({
	selector: 'app-question',
	templateUrl: './question.component.html',
	styleUrls: ['./question.component.css']
})
export class QuestionComponent implements OnInit {
	module: any
	title: any = ''
	question: any = ''
	shortDescription: any = ''
	options: any = []
	index: any;
	nextModule = '';
	btnType = '';
	isQuesClick = false;
	nextQues = '';
	questionId = [1, 2, 3, 4, 5, 6];
	optionData = {};
	moduleType = { 'module-1': 'M-1', 'module-2': 'M-2', 'module-3': 'M-3', 'module-4': 'M-4' };

	constructor(public questionnaireService: QuestionnaireService,
		public route: ActivatedRoute, public router: Router, ) { }

	ngOnInit() {
		this.router.routeReuseStrategy.shouldReuseRoute = () => {
			// do your task for before route
			return false;
		}

		this.module = this.route.snapshot.paramMap.get("module");
		this.index = this.route.snapshot.paramMap.get("index");

		if (this.index == undefined || this.index == '') {
			this.index = 0;
		}

		this.getQuestionnaire();
	}

	getQuestionnaire() {
		let id = this.questionId[this.index - 1], type;
		type = this.moduleType[this.module];
		console.log(id, this.index);
		if ((id != undefined || id != null) && (type != undefined || type != null))

			this.questionnaireService
				.getQuestionnaire({ question_id: id, type: type })
				.subscribe(res => {
					if (res['status'] == 'success') {
						this.title = res['question']['title'];
						this.question = res['question']['question'];
						this.question = res['question']['question'];
						this.optionData['questions_id'] = res['question']['id'];
						this.options = res['question']['options'];

						this.shortDescription = res['question']['short_description'];

						this.nextQues = res['question']['next_question'];
						this.nextModule = res['question']['next_module'];

						this.btnType = this.nextQues ? 'question' : 'module';

					}
				});
	}

	saveAnswer(option: any) {
		let id = 'option-' + option.id, op, btns;

		btns = document.querySelectorAll("button[id^='option']");

		btns.forEach(btn => {
			if (btn.id != id) {
				//btn.disabled = true;
				btn.classList.remove('btn-success');
				btn.classList.remove('btn-warning');
				btn.classList.remove('btn-wrong');
				btn.classList.add('btn-default');
				option.answer_status == 'correct' && (btn.disabled=true);
				btn.innerHTML = (btn as HTMLButtonElement).getAttribute('data_label');
				//console.log((btn as HTMLButtonElement).getAttribute('data_label'));
			} else {
				btn.classList.remove('btn-default');
				switch (option.answer_status) {
					case 'correct': {
						btn.classList.add('btn-success');
						btn.innerHTML = 'Correct';
						break;
					}
					case 'partly_correct': {
						btn.classList.add('btn-warning');
						btn.innerHTML = 'Partly correct';
						break;
					}
					case 'wrong': {
						btn.classList.add('btn-wrong');
						btn.innerHTML = 'Wrong';
						break;
					}
				}
			}
			// btn.id != id && btn.classList.add('btn-default');
		});

		op = document.getElementById(id);

		this.optionData['options_id'] = option['id'];
		this.optionData['response'] = option['option_label'];

		console.log('lick>>', option,op);
		// switch (option.answer_status) {
		// 	case 'correct': {
		// 		op.classList.add('btn-success');
		// 		op.innerHTML = 'Correct';
		// 		break;
		// 	}
		// 	case 'partly_correct': {
		// 		op.classList.add('btn-warning');
		// 		op.innerHTML = 'Partly correct';
		// 		break;
		// 	}
		// 	case 'wrong': {
		// 		op.classList.add('btn-wrong');
		// 		op.innerHTML = 'Wrong';
		// 		break;
		// 	}
		// }
		this.isQuesClick = true;
	}

	nextAction() {
		let url = '/patient/';
		url += this.btnType == 'question' ? this.module + '/question/' + this.nextQues : this.nextModule + '/intro';
		url = (this.btnType != 'question' && this.module == 'module-3') ? '/patient/' + this.module + '/case-study/2' : url;
		this.router.navigate([url]);
		// this.questionnaireService.saveQuestionnaire(this.optionData).subscribe(res => {
		// 	if (res['status'] == 'success') {
		// 		this.router.navigate([url]);
		// 	}
		// });
	}

	getSomeClass(text) {
		const isValid = text.length > 27;
		return { 'font-24': isValid };
	}

	removeIndex(text) {
		return text.split(") ")[1];
	}
}
