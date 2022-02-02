import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Ng4LoadingSpinnerService } from 'ng4-loading-spinner';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from '../../service/auth.service';

@Component({
	selector: 'app-home',
	templateUrl: './home.component.html',
	styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
	lat: Number = 25.7870883;
	lng: Number = -80.2151046;
	styleArray = [
		{ 'elementType': 'geometry', 'stylers': [{ 'color': '#f5f5f5' }] },
		{ 'elementType': 'labels.icon', 'stylers': [{ 'visibility': 'off' }] },
		{ 'elementType': 'labels.text.fill', 'stylers': [{ 'color': '#616161' }] },
		{ 'elementType': 'labels.text.stroke', 'stylers': [{ 'color': '#f5f5f5' }] },
		{ 'featureType': 'road', 'elementType': 'geometry', 'stylers': [{ 'color': '#ffffff' }] },
		{ 'featureType': 'water', 'elementType': 'geometry', 'stylers': [{ 'color': '#e77a28' }] },
	];
	public loginForm: FormGroup;
	data: any;

	constructor(private router: Router,
		private authService: AuthService,
		public toastr: ToastrService,
		private spinnerService: Ng4LoadingSpinnerService) { }

	ngOnInit() {
		this.loginForm = new FormGroup({
			username: new FormControl('', { validators: [Validators.required] }),
			password: new FormControl('', { validators: [Validators.required] }),
			remember_me: new FormControl("")
		});
		this.checkCookie();
		if (localStorage.getItem('token') && localStorage.getItem('role')) {
			this.router.navigate(['/patient/landing']);
		}
	}


	signIn() {
		if (this.loginForm.valid) {
			this.setCookie("username", "password", this.loginForm.value.remember_me ? 365 : -1);
			this.spinnerService.show();
			this.authService.login(this.loginForm.value).subscribe(
				result => {
					this.data = result;
					if (this.data.status === 'success') {
						let is_invite_singup=this.data.is_invite_singup;
						let is_demographic_done=this.data.is_demographic_done;
						localStorage.setItem('token', this.data.token);
						localStorage.setItem('role', this.data.role);
						localStorage.setItem('is_invite_singup', is_invite_singup);
						localStorage.setItem('is_demographic_done', is_demographic_done);
						localStorage.setItem('username', this.data.username);

						let route =is_demographic_done?(is_invite_singup?'/patient/landing':'/patient/module-1/intro'):'/patient/demographic';

						this.router.navigate([route]).then(() => {
							this.toastr.success(this.data.msg, null);
						});
					} else {
						this.toastr.error(this.data.msg, null);
					}
					this.spinnerService.hide();
				},
				err => {
					console.log('Error', err);
				}
			);
		}
	}

	scroll(el) {
		el.scrollIntoView({ behavior: 'smooth' });
	}


	setCookie(username, password, exdays) {
		var d = new Date();
		d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
		var expires = "expires=" + d.toUTCString();
		document.cookie =
			username +
			"=" +
			this.loginForm.value.username +
			";" +
			expires +
			";path=/";
		document.cookie =
			password +
			"=" +
			this.loginForm.value.password +
			";" +
			expires +
			";path=/";
	}

	getCookie(cname) {
		let name = cname + "=";
		let ca = document.cookie.split(";");

		for (let i = 0; i < ca.length; i++) {
			let c = ca[i];
			while (c.charAt(0) == " ") {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

	checkCookie() {
		let username = this.getCookie("username");
		let password = this.getCookie("password");
		if (username != "") {
			this.loginForm.controls["username"].setValue(username);
			this.loginForm.controls["password"].setValue(password);
			this.loginForm.controls["remember_me"].setValue(true);
		} else {
			if (username != "" && username != null) {
				this.setCookie("username", "password", 365);
			}
		}
	}
}