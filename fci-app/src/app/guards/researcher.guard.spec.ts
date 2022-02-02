import { TestBed, async, inject } from '@angular/core/testing';

import { ResearcherGuard } from './researcher.guard';

describe('ResearcherGuard', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ResearcherGuard]
    });
  });

  it('should ...', inject([ResearcherGuard], (guard: ResearcherGuard) => {
    expect(guard).toBeTruthy();
  }));
});
