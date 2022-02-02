import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormControl, FormGroup, Validators } from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { Ng4LoadingSpinnerService } from 'ng4-loading-spinner';
import { AuthService } from '../../service/auth.service';

@Component({
	selector: 'app-forgot-password',
	templateUrl: './forgot-password.component.html',
	styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent implements OnInit {
	form: FormGroup;
	is_unique_email: Boolean = false;
	is_unique_email_msg = '';
	is_success = false;
	constructor(
		private formBuilder: FormBuilder,
		private authService: AuthService,
		public toastr: ToastrService,
		private spinnerService: Ng4LoadingSpinnerService) { }

	ngOnInit() {
		this.form = this.formBuilder.group(
			{
				email: new FormControl('', {
					validators: [
						Validators.required,
						Validators.pattern(
							/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						),
					],
				}),
			},
			{
				updateOn: 'blur'
			}
		);
	}

	forgotPassword() {
		this.is_unique_email_msg = '';

		if (this.form.valid) {
			this.spinnerService.show();

			this.authService.forgot_password(this.form.value).subscribe(
				result => {
					const response = result;
					if (response['status'] !== 'error') {
						this.is_success = true;
					} else {
						this.is_success = false;
						this.is_unique_email_msg = 'This Email address does not exist in system..';
					}
					this.spinnerService.hide();
				},
				err => {
					console.log(err);
					this.spinnerService.hide();
				}
			);
		}
	}

}
