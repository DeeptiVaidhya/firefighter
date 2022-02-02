import { Component, OnInit } from '@angular/core';
import { ActivatedRoute,NavigationEnd, Router } from '@angular/router';

@Component({
  selector: 'app-fire-service',
  templateUrl: './fire-service.component.html',
  styleUrls: ['./fire-service.component.css']
})

export class FireServiceComponent implements OnInit {

  module = 'module-3';
  title = 'Module 3';
  index: any = 0;
 

  public constructor(private route: ActivatedRoute, private router: Router) {
      this.module = this.route.snapshot.paramMap.get("module");
      this.index = this.route.snapshot.paramMap.get("index");
      this.title = this.module.replace(/[\-]/g, ' ');    
      if (this.title) {
        this.title = this.title.charAt(0).toUpperCase() + this.title.slice(1);
      }
  }

 ngOnInit() {
    this.router.routeReuseStrategy.shouldReuseRoute = () => {
      return false;
    }
  }

}
