import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable()
export class DataService {
	public messageSource = new BehaviorSubject<string>('');
	public ResoursesID = new BehaviorSubject<string>('');
	public currentMessage = this.messageSource.asObservable();
	public currentResource = this.ResoursesID.asObservable();

	constructor() {}

	changeMessage(message: string) {
		this.messageSource.next(message);
	}

	navigaeToResource(id: string){
		this.ResoursesID.next(id);
	}
}

