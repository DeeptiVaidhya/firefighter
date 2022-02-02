import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FirefighterExposedComponent } from './firefighter-exposed.component';

describe('FirefighterExposedComponent', () => {
  let component: FirefighterExposedComponent;
  let fixture: ComponentFixture<FirefighterExposedComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FirefighterExposedComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FirefighterExposedComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
