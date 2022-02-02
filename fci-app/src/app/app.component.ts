import { Component, Output } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { DEFAULT_INTERRUPTSOURCES, Idle } from "@ng-idle/core";
import { ToastrService } from 'ngx-toastr';
import { CONSTANTS } from "./config/constants";
import { AuthService } from './service/auth.service';
import { DataService } from './service/data.service';

@Component({
	selector: 'app-root',
	templateUrl: './app.component.html',
	styleUrls: ['./app.component.css'],
})
export class AppComponent {

	previous_title: string;
	previous_link: string;
	title = 'FCI';
	role: any;
	link: any;
	idleState = "Not started.";
	timedOut = false;
	interval: any;

	maIsOpen: boolean;
	public isLoggedIn: Boolean = true;

	@Output() headerTitle: string;
	isHeaderHidden = false;
	pathsWtLogin: any = [
		'/home',
		'/about-us',
		'/contact-us',
		'/faq',
		'/terms-conditions',
		'/privacy-policy',
		'/create-password',
		'/forgot-password',
		'/reset-password',
	];
	isModulePage = false;
	isLandingPage = false;
	
	constructor(
		private router: Router,
		private authService: AuthService,
		public toastr: ToastrService,
		public dataService: DataService,
		private idle: Idle
	) {

		// sets an idle timeout of 5 seconds, for testing purposes.
		idle.setIdle(CONSTANTS.SESSION_TIMEOUT);
		// sets a timeout period of 5 seconds. after 10 seconds of inactivity, the user will be considered timed out.
		idle.setTimeout(0);
		// sets the default interrupts, in this case, things like clicks, scrolls, touches to the document
		idle.setInterrupts(DEFAULT_INTERRUPTSOURCES);

		//idle.onIdleEnd.subscribe(() => (this.idleState = 'No longer idle.'));
		idle.onTimeout.subscribe(() => {
			this.idleState = 'Timed out!';
			this.timedOut = true;
		});
		idle.onIdleStart.subscribe(() => {
			this.authService.logout().subscribe(
				result => {
					if (result.status === 'success') {
						localStorage.removeItem('token');
						this.idleState = 'You\'ve gone idle!';
						localStorage.clear();
						this.timedOut = true;
						this.isLoggedIn = false;
						clearInterval(this.interval);
						this.router.navigate(['/home']).then(() => {
							this.toastr.error(
								'Your session has been expired. Please log in to continue.'
							);
						});
					}
				},
				err => {
					console.log(err);
				}
			);
		});
	}

	headerTitleChange(title) {
		return title;
	}

	checkDashLink() {
		this.role = localStorage.getItem('role');
		switch (this.role) {
			case '3':
				this.link = {
					// dashboard: 'provider/dashboard',
					dashboard: 'patient/dashboard',
					profile: 'patient/profile',
				};
				break;
			default:
				this.link = {};
				break;
		}
	}

	ngOnInit() {
		this.checkDashLink();
		this.headerTitle = this.previous_title;
		this.router.events.subscribe(evt => {
			if (!(evt instanceof NavigationEnd)) {
				return;
			}
			this.authService.checkLogin().subscribe(response => {
				//console.log(response,this.router);
				if (response.hasOwnProperty('status') && response['status'] == 'INVALID_TOKEN') {
					this.isLoggedIn = false;
					localStorage.clear(); // forcefully logout and clear localstorage, if token not found
					clearInterval(this.interval);
					if (
						!this.pathsWtLogin.some(path => {
							return this.router.url.indexOf(path) === 0; // current path starts with this path string
						})
					) {
						this.router.navigate(['/home']);
					}
				} else {
					this.isLoggedIn = true;
				}
			});
			this.checkDashLink();
			//console.log(this.router.url);
			this.maIsOpen = this.router.url.indexOf('/my-account') === 0;
			//console.log(this.maIsOpen);
			this.isHeaderHidden = ['/forgot-password', '/create-password', '/reset-password', '/patient/landing'].some(path => {
				return this.router.url.indexOf(path) === 0; // current path starts with this path string
			})
				? true
				: false;

			this.isModulePage = this.router.url.indexOf('/patient/module') === 0;
			document.querySelector('html').style.height = (this.isModulePage || this.router.url.indexOf('/patient/landing') === 0) ? '100%' : 'auto';
			this.isLandingPage = this.router.url.indexOf('/patient/landing') === 0;
			
			window.scrollTo(0, 0);

			clearInterval(this.interval);
			this.interval = setInterval(() => {
				if (
					this.isLoggedIn &&
					document.hasFocus()
				) {
					this.authService
						.updatesessionTime({
							showSpinner: false,
							focus: true
						})
						.subscribe();
				}
			}, 10000);
		});

		console.log('appL>>', this.isLoggedIn, 'appM>>', this.maIsOpen, 'appLink>>', this.link, 'app>>Head', this.isHeaderHidden);
	}
}
