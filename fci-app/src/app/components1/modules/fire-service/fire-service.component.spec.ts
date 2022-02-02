import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { fire-serviceComponent } from './fire-service.component';

describe('fire-serviceComponent', () => {
  let component: fire-serviceComponent;
  let fixture: ComponentFixture<fire-serviceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ fire-serviceComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(fire-serviceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
