import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot } from '@angular/router';
import { Observable } from 'rxjs';

@Injectable()
export class SurvivorGuard implements CanActivate {
	canActivate(
		next: ActivatedRouteSnapshot,
		state: RouterStateSnapshot): Observable<boolean> | Promise<boolean> | boolean {
		// return false;

		console.log('Survivor',JSON.parse(localStorage.getItem('is_invite_singup')));

		if (JSON.parse(localStorage.getItem('is_invite_singup'))==true) {
			return !0;
		}
		return !1;
	}
}
