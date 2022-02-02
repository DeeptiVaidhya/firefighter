import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { OccupationalExposureComponent } from './occupational-exposure.component';

describe('OccupationalExposureComponent', () => {
  let component: OccupationalExposureComponent;
  let fixture: ComponentFixture<OccupationalExposureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ OccupationalExposureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(OccupationalExposureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
