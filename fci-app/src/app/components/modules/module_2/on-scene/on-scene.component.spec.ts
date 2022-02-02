import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { OnSceneComponent } from './on-scene.component';

describe('OnSceneComponent', () => {
  let component: OnSceneComponent;
  let fixture: ComponentFixture<OnSceneComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ OnSceneComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(OnSceneComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
