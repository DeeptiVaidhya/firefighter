import { Pipe, PipeTransform } from "@angular/core";
import { DomSanitizer } from '@angular/platform-browser';

@Pipe({ name: 'safeHtml' })
export class SafeHtmlPipe implements PipeTransform {
  constructor(private sanitized: DomSanitizer) { }
  transform(value) {
    var elems = document.querySelectorAll('a');
    var index = 0, length = elems.length;
    for (; index < length; index++) {
      if (!elems[index].href.includes('#') && !elems[index].href.includes('javascript:void(0)')) {
        elems[index].style.textDecoration = 'underline';
      }
    }
    return this.sanitized.bypassSecurityTrustHtml(value);
  }
}

