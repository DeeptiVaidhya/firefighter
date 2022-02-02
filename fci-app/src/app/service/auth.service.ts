import { Injectable } from "@angular/core";
import { Observable } from "rxjs";
import { HelperService } from "./helper.service";

@Injectable()
export class AuthService {
	constructor(public helperService: HelperService) { }

	/**
	 * @desc Calling api for for user login with username/email and password
	 * @param credentials
	 */
	login(credentials): Observable<any> {
		return this.helperService.makeHttpRequest('auth/login', 'post', credentials, !0);
	}

	/**
	 * @desc Function is used to check a user is logged in or not called every time
	 * 		 when a user performs any activity in application
	 */
	checkLogin(): Observable<any> {
		return this.helperService.makeHttpRequest('auth/check-login', 'post', {}, !0);
	}

	/**
	 * @desc Check a user's email is unique or not, called when adding a patient/provider from research staff or from edit profile
	 * @param data
	 */
	isEmailUnique(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/check-email', 'post', data, !0);
	}

	/**
	 * @desc Getting users according to their type like patient/research-staff/provider
	 */
	getUser(user_type, is_active = false): Observable<any> {
		return this.helperService.makeHttpRequest('user/user/?user_type=' + user_type + (is_active ? '&is_active=!0' : ''), 'get', {}, !0);
	}

	/**
	 * @desc Function is used to get user details for a paticular user.
	 * @param input
	 */
	getUserDetail(data): Observable<any> {
		return this.helperService.makeHttpRequest('user/user-detail', 'post', data, !0);
	}


	/**
	 * @desc Get profile details for a patient/provider/researcher
	 */
	get_profile(): Observable<any> {
		return this.helperService.makeHttpRequest('auth/profile','get',{},!0);
	}

	/**
	 * @desc Cheching a password entered is correct or not, called from edit profile page
	 * @param data
	 */
	isCurrentPassword(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/check-password','post',data,!0);
	}

	/**
	 * @desc Cheching a password entered is previous or not, called from edit profile page
	 * @param data
	 */
	isPreviousPassword(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/check-previous-password','post',data,!0);
	}

	/**
	 * @desc Updating profile for each user type like research-staff, patient and provider
	 * @param data
	 */
	update_profile(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/profile','post',data,!0);
	}

	/**
	 * @desc Logout function for each user.
	 */
	logout(): Observable<any> {
		return this.helperService.makeHttpRequest('auth/logout','get',{},!0);
	}

	/**
	 * @desc Logout function for each user.
	 */
	updatesessionTime(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/update-session-time','post',data,!0);
	}

	/**
	 * @desc Update a user like patient/provider, called from research staff
	 * @param data
	 */
	updateUser(data) {
		return this.helperService.makeHttpRequest('user/user','put',data,!0);
	}

	/**
	 * @desc create user password
	 */
	change_password(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/reset-password','post',data);
	}

	/**
	 * @desc check reset password code
	 */
	reset_password_code(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/reset-password-code','post',data);
	}

	/**
	 * @desc Calling api for forgot Password
	 * @param data
	 */
	forgot_password(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/forgot-password','post',data);
	}

	/**
	 * @desc Calling api for Contact US
	 * @param data
	 */
	contact_us(data): Observable<any> {
		return this.helperService.makeHttpRequest('auth/contact-us','post',data);
	}

	/**
	 * @desc add user
	 * @param data
	 */
	add_user(data): Observable<any> {
		console.log('s',data);
		return this.helperService.makeHttpRequest('auth/sign-up','post',data);
	}

	/**
	 * @desc country list
	 * @param data
	 */
	country_list(): Observable<any> {
		return this.helperService.makeHttpRequest('auth/country-list','get',{});
	}
}