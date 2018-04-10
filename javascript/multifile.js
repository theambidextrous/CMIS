/**
 * Licence:
 *   Use this however/wherever you like, just don't blame me if it breaks anything.
 *
 * Credit:
 *   If you're nice, you'll leave this bit:
 *  
 *   Class by Stickman -- http://www.the-stickman.com
 *      with thanks to:
 *      [for Safari fixes]
 *         Luis Torrefranca -- http://www.law.pitt.edu
 *         and
 *         Shawn Parker & John Pennypacker -- http://www.fuzzycoconut.com
 *      [for duplicate name bug]
 *         'neal'
 */
function MultiSelector(e,t){this.list_target=e,this.count=0,this.id=0,t?this.max=t:this.max=-1,this.addElement=function(e){"INPUT"==e.tagName&&"file"==e.type?(e.name="file["+this.id+++"]",e.multi_selector=this,e.onchange=function(){var e=document.createElement("input");e.type="file",this.parentNode.insertBefore(e,this),this.multi_selector.addElement(e),this.multi_selector.addListRow(this),this.style.position="absolute",this.style.left="-1000px"},-1!=this.max&&this.count>=this.max&&(e.disabled=!0),this.count++,this.current_element=e):alert("Error: not a file input element")},this.addListRow=function(e){var t=document.createElement("li"),i=document.createElement("i");i.title="Delete",i.className="fa fa-window-close",t.element=e,i.onclick=function(){return this.parentNode.element.parentNode.removeChild(this.parentNode.element),this.parentNode.parentNode.removeChild(this.parentNode),this.parentNode.element.multi_selector.count--,this.parentNode.element.multi_selector.current_element.disabled=!1,!1},t.innerHTML=e.value,t.appendChild(i),this.list_target.appendChild(t)}}var multi_selector=new MultiSelector(document.getElementById("files_list"),3);multi_selector.addElement(document.getElementById("element_input"));