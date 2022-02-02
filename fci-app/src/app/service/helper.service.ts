import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Ng4LoadingSpinnerService } from 'ng4-loading-spinner';
// import { ToastrService } from 'ngx-toastr';
import { catchError, map } from 'rxjs/operators';
import { CONSTANTS } from '../config/constants';


@Injectable()
export class HelperService {
    isLoggedIn: boolean = false;
    constructor(private http: HttpClient,
        private spinner: Ng4LoadingSpinnerService
    ) { }


	/**
	 * Get token and send it to interceptor
	 */
    getToken() {
        return localStorage.getItem('token');
    }

	/**
	 * Check if the API is required for Token
	 */
    public isAuthenticated(): boolean {
        return this.isLoggedIn;
    }

	/**
	 * @desc Common function to call GET/POST with/without parameters
	 * @param url
	 * @param type
	 * @param data
	 * @param isLoggedIn
	 */
    makeHttpRequest(url, type = 'get', data = {}, isLoggedIn = false) {
        let httpRequest: any;
        this.isLoggedIn = isLoggedIn;
        url = CONSTANTS.API_ENDPOINT + url;

        if (type == 'post') {
            httpRequest = this.http[type](url, data);
        } else {
            httpRequest = this.http[type](url);
        }
        (data['showSpinner'] == undefined || (data['showSpinner'] && data['showSpinner'] != false)) && this.spinner.show();;
        return httpRequest.pipe(
            map(res => {
                let response = res;
                (data['showSpinner'] == undefined || (data['showSpinner'] && data['showSpinner'] != false)) && this.spinner.hide();;
                return response;
            }),
            catchError(err => {
                return err;
            })
        );
    }

    /**
     * empty array check;
     */
    isEmptyArr(emptyArray) {
        return Array.isArray(emptyArray) && emptyArray.length;
    }

    // showAward(week: any, type: string, i: any, armAlloted = 'INTERVENTION') {
    //     switch (type) {
    //         case 'ribbon': {
    //             return (week['week_number'] >= i + 1 && week['total_time_spent_in_week'] >= 10) ? '' : 'disabled'
    //         }
    //         case 'medal': {
    //             return (week['week_number'] >= i + 1 && week['total_time_spent_in_week'] >= 20) ? '' : 'disabled'
    //         }
    //         case 'trophy': {
    //             return (week['week_number'] >= i + 1 && week['total_time_spent_in_week'] >= 30) ? '' : 'disabled'
    //         }
    //         default: {
    //             return 'disabled';
    //         }
    //     }
    // }
}


