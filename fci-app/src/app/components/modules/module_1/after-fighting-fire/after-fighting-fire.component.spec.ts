import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AfterFightingFireComponent } from './after-fighting-fire.component';

describe('AfterFightingFireComponent', () => {
  let component: AfterFightingFireComponent;
  let fixture: ComponentFixture<AfterFightingFireComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AfterFightingFireComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AfterFightingFireComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
