import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PreviousNextButtonsComponent } from './previous-next-buttons.component';

describe('PreviousNextButtonsComponent', () => {
  let component: PreviousNextButtonsComponent;
  let fixture: ComponentFixture<PreviousNextButtonsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PreviousNextButtonsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PreviousNextButtonsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
