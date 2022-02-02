import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { DataService } from '../../../service/data.service';

@Component({
	selector: 'app-inner-page-header',
	templateUrl: './inner-page-header.component.html',
	styleUrls: ['./inner-page-header.component.css'],
})
export class InnerPageHeaderComponent implements OnInit {
	@Input() page_title;
	@Input() back_link;
	@Input() back_title = '';
	@Input() count: number;
	@Input() isHidden = false;
	@Output() headerTitleChange = new EventEmitter<any>();
	title: string;
	link: string;
	role = '';
	constructor(private dataService: DataService) { }

	ngOnInit() {
		this.dataService.currentMessage.subscribe(message => {
			var dataarray = message ? message.split("::") : {};

			if (dataarray[0] === 'undefined') {
				this.title = 'Dashboard';
			} else {
				this.title = dataarray[0];
			}
			if (dataarray[1] === 'undefined') {
				this.link = '/patient/dashboard';
			} else {
				this.link = dataarray[1];
			}



		});

		if (this.back_title.toLowerCase() == 'home') {
			this.back_title = 'Dashboard';
			localStorage.getItem('role') && (this.role = localStorage.getItem('role'));
			switch (this.role) {
				case '2':
					this.back_link = '/researcher/dashboard';
					break;
				case '3':
					// this.back_link = '/provider/dashboard';
					this.back_link = '/patient/dashboard';
					break;
				case '4':
					this.back_link = '/patient/dashboard';
					break;
				default:
					this.back_link = '/home';
					this.back_title = 'Home';
					break;
			}
		}
	}
}
