import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HelperService } from './helper.service';

@Injectable()
export class QuestionnaireService {
	constructor(public helperService: HelperService) { }

	/**
	 *
	 * @param data
	 * Get Chapter/Sub-topic details
	 */
	chapterDetails(data): Observable<any> {
		return this.helperService.makeHttpRequest('educational/chapter-details', 'post', data, !0);
	}

	/**
	 *
	 * @param data
	 * Toggle favorite for a sub topic and a user
	 */
	// updateFavorite(data): Observable<any> {
	//     return this.helperService.makeHttpRequest('educational/update-favorite','post',data,!0);
	// }

	/**
	 * @desc Function is used to show total questionnaire completed within current week.
	 */
	patients_weekly_questionnaire() {
		return this.helperService.makeHttpRequest('questionnaire/dash-weekly-questionnaire', 'get', {}, !0);
	}

	/**
	 * @param data
	 * @desc Function is used to save questionnaire answer given by user.
	 */
	saveQuestionnaire(data) {
		return this.helperService.makeHttpRequest('questionnaire/save-answer', 'post', data, !0);
	}

	/**
	 * @param data
	 * @desc Function is used to get questionnaire answer given by user.
	 */
	getQuestionnaire(data) {
		return this.helperService.makeHttpRequest('questionnaire/questionnaire', 'post', data, !0);
	}

	/**
	 * @param data
	 * @desc Function is used to get demographic questions.
	 */
	getDemographic() {
		return this.helperService.makeHttpRequest('questionnaire/demographic', 'get', {}, !0);
	}

	/**
	 * @desc Function is used to get current running week information.
	 */
	get_current_week() {
		return this.helperService.makeHttpRequest('questionnaire/questionnaire/week-info', 'get', {}, !0);
	}

	/**
	 * @desc Function is used to get resourses data
	 */
	get_resources() {
		return this.helperService.makeHttpRequest('educational/resources', 'get', {}, !0);
	}

	/**
	 * @desc Function is used to get resourses data
	 */
	getResourceQuestion(data) {
		return this.helperService.makeHttpRequest('questionnaire/resourse-question', 'post', data, !0);
	}

	/**
	 * @desc Function is used to get resourses responses given by participants
	 */
	getResourceQuestionResponses(data) {
		return this.helperService.makeHttpRequest('questionnaire/resourse-question-responses', 'post', data, !0);
	}


	/**
	 * @desc Function is used to get bluejeans Session
	 */
	submitResourceQuestionResponse(data) {
		return this.helperService.makeHttpRequest('questionnaire/resourse-question-response', 'post', data, !0);
	}

	/**
	 * @desc Function is used to add chapter subtopic as visited
	 */
	addVisitedChapter(data) {
		return this.helperService.makeHttpRequest('questionnaire/add-to-visited', 'post', data, !0);
	}

	/**
	 * @desc Function is used to add chapter subtopic as visited
	 */
	updateVisitedChapter(data) {
		return this.helperService.makeHttpRequest('questionnaire/update-visited-chapter-subtopic', 'post', data, !0);
	}

	/**
	 * @desc Function is used to add resource as visited
	 */
	addResourceVisited(data) {
		return this.helperService.makeHttpRequest('questionnaire/add-visited-resource', 'post', data, !0);
	}

	/**
	 *
	 * @param data
	 * Get Exercise details with pdf
	 */
	exerciseDetails(data): Observable<any> {
		return this.helperService.makeHttpRequest('educational/exercise-details', 'post', data, !0);
	}	

	saveDemographic(data): Observable<any> {
		return this.helperService.makeHttpRequest('questionnaire/save-demographic', 'post', data, !0);
	}

	moduleAudio(data): Observable<any> {
		return this.helperService
				.makeHttpRequest('educational/module_audio', 'post', data, !0)
				
	}

}
