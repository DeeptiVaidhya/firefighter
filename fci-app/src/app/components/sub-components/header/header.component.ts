import { Component, Input, OnInit, TemplateRef } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { BsModalRef, BsModalService } from 'ngx-bootstrap/modal';
import { ToastrService } from 'ngx-toastr';
import { AuthService } from '../../../service/auth.service';
import { QuestionnaireService } from '../../../service/questionnaire.service';
import { HelperService } from '../../../service/helper.service';

@Component({
	selector: 'app-header',
	templateUrl: './header.component.html',
	styleUrls: ['./header.component.css'],
})
export class HeaderComponent implements OnInit {

	@Input() isLoggedIn;
	@Input() isHeaderHidden = true;
	@Input() isModulePage = false;
	@Input() maIsOpen;
	@Input() link;
	public isCollapsed: boolean = true;
	public isCollapsedModule: boolean = true;
	maClicked: boolean = false;
	data: any;
	modalRef: BsModalRef;
	registrationForm: FormGroup;
	countryName: any;


	constructor(private router: Router, 
		private authService: AuthService,
		private questionnaireService: QuestionnaireService,
		public toastr: ToastrService,
		private modalService: BsModalService,
		private formBuilder: FormBuilder,
		public helper: HelperService) {
	}
	toggleDropdown(): void {
		this.maClicked = !this.maClicked;

	}

	ngOnInit() {
		this.registrationForm = this.formBuilder.group(
			{
				first_name: ['', Validators.compose([Validators.pattern(/^[a-zA-Z]+[a-zA-Z '".-]{2,30}$/), Validators.required])
				],
				email: [
					'',
					Validators.compose([
						Validators.pattern(
							/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
						),
						Validators.required,
					]),
				],
				country: ['', Validators.required],
				address: [''],
				password: [
					'',
					Validators.compose([
						Validators.pattern(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/),
						Validators.required,
					]),
				],
				confirm_password: ['']
			},
			{
				validator: this.match_password
			
			}
		);

		this.get_country_list();
	}
	match_password(g: FormGroup) {
		return g.get('password').value === g.get('confirm_password').value ? null : { 'match_password': true };
	}
	get_country_list() {
		this.authService.country_list().subscribe(
			response => {
				if (response.status == 'success') {
					this.countryName = response.data;
				}
			},
			error => { }
		);
	}

	toggleModuleMenu() {
		this.isCollapsedModule = !this.isCollapsedModule;
	}

	submit() {
		if (this.registrationForm.valid) {
			let param = this.registrationForm.value;
			param['showSpinner'] = true;

			this.authService.add_user(param).subscribe(
				response => {
					if (response.status == 'success') {
						this.toastr.success(response.msg, null);
						this.modalRef.hide();
					} else {
						let msg =response.status == 'error'?response.msg:response.data.email;
						this.toastr.error(msg, null);
					}
				},
				error => { }
			);
		} else {
			let scrollField = '';
			Object.keys(this.registrationForm.controls).forEach(field => {
				let control = this.registrationForm.get(field);
				if (!scrollField && control.invalid) {
					scrollField = field;
				}
				control.markAsTouched({ onlySelf: true });
			});
		}
	}

	logout() {
		this.authService.logout().subscribe(
			result => {
				this.data = result;
				if (this.data.status === 'success') {
					localStorage.removeItem('token');
					localStorage.clear();
					this.router.navigate(['/home']).then(() => {
						this.toastr.success(this.data.msg, null);
					});
				}
			},
			err => {
				console.log(err);
			}
		);
	}

	scroll(el) {
		// if not home page, then first to Home then scroll down
		if (this.router.url !== '/home') {
			this.router.navigate(['/home']).then(() => {
				document.getElementById(el).scrollIntoView({ behavior: "smooth" });
			});
		} else {
			document.getElementById(el).scrollIntoView({ behavior: "smooth" });
		}
	}

	openModal(template: TemplateRef<any>) {
		this.modalRef = this.modalService.show(template);
			// this.modalRef = this.modalService.show(template , {class: 'modal-lg'});
	}
} 


