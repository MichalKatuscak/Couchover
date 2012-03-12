Couchover = {}

/********** Element **********/

/**
 *  Search element in document
 *  
 *  @return object Couchover.Element
 */
Couchover.Element = function (name) {
    var first_letter = name.substring(0,1);  
    
    if (first_letter == '#') {    
        var name = name.substring(1); 
        this.element = document.getElementById(name);
    } else {
        this.element = document.getElementsByTagName(name)[0];
    }
    
    if (this.element) return this;
    return false;
}

/**
 *  Get HTML from element
 *  
 *  @return string HTML
 */
Couchover.Element.prototype.getHTML = function () {
    return this.element.innerHTML;
}  

/**
 *  Set HTML in element
 *  
 *  @param string HTML
 *  @return string HTML
 */
Couchover.Element.prototype.setHTML = function (HTML) {
    return this.element.innerHTML = HTML;
}

/**
 *  Get text from element
 *  
 *  @return string text 
 */
Couchover.Element.prototype.getText = function () {
    return this.element.innerText;
}

/**
 *  Set text in element
 *  
 *  @param string text
 *  @return string text 
 */
Couchover.Element.prototype.setText = function (text) {
    return this.element.innerText = text;
}

/**
 *  Remove element
 */
Couchover.Element.prototype.remove = function () {
    this.element.parentNode.removeChild(this.element);
}

/**
 *  Hide element 
 *   
 *  @return object Couchover.Element
 */
Couchover.Element.prototype.hide = function () {
    this.element.style.display = 'none';
    return this;
}

/**
 *  Show element, default as type 'block'
 *   
 *  @param string type
 *  @return object Couchover.Element
 */
Couchover.Element.prototype.show = function (type) {
    type = type ? type : 'block';
    this.element.style.display = type;
    return this;
}

/**
 *  Get node of element
 *  
 *  @return object node
 */
Couchover.Element.prototype.getElement = function () {
    return this.element;
}

/**
 *  Append child to element
 */
Couchover.Element.prototype.addChild = function (type, id, style) {
    var child = document.createElement(type);
    child.id = id;
    for (var name in style) {
        child.style[name] = style[name];
    }
    this.element.appendChild(child);
}


/********** Body **********/

/**
 *  Search body in document and set elements of shadow and dialog
 *  
 *  @return object Couchover.Body
 */
Couchover.Body = function () {
    this.body = new Couchover.Element('body');
    this.elements = {}
    this.elements.shadow = new Couchover.Element('#couchover-body-shadow');
    this.elements.dialog = new Couchover.Element('#couchover-body-dialog');
    return this;
}

/**
 *  Show shadow
 *  
 *  @param string color
 */  
Couchover.Body.prototype.showShadow = function (color) {
    color = color ? color : 'black';
    this.body.addChild ('div', 'couchover-body-shadow', {
        position: 'fixed',
        top: '0px',
        left: '0px',
        width: '100%',
        height: '100%',
        backgroundColor: color,
        opacity: '0.6'
    }); 
    
    this.elements.shadow = new Couchover.Element('#couchover-body-shadow');
    
    this.elements.shadow.getElement().onclick = function () { 
        new Couchover.Body().hideShadow() 
    };
    
    return this;
}

/**
 *  Hide shadow and dialog when exists
 */
Couchover.Body.prototype.hideShadow = function () {                        
    if (this.elements.dialog.getElement()) this.elements.dialog.remove();
    if (this.elements.shadow.getElement()) this.elements.shadow.remove();
}

/**
 *  Hide shadow and dialog when exists
 */
Couchover.Body.prototype.hideDialog = function () {           
    if (this.elements.dialog.getElement()) this.elements.dialog.remove();
    if (this.elements.shadow.getElement()) this.elements.shadow.remove();
}

/**
 *  Show dialog
 *  
 *  @param string title
 *  @param string text
 *  @param int width
 */
Couchover.Body.prototype.showDialog = function (title, text, width) {
    width = width ? width : 400;
    
    var position_left = (this.body.getElement().offsetWidth - width) / 2;
    
    if (!Couchover.Element('#couchover-body-shadow')) {
        this.showShadow();
    }
    
    this.body.addChild ('div', 'couchover-body-dialog', {
        position: 'fixed',
        top: '100px',
        left: position_left+'px',
        width: width+'px',
        maxHeight: '500px',
        backgroundColor: 'white',
        border: '1px black solid',
        boxShadow: '#333 0 0 10px',
        font: '12px Sans-serif'
    });
    
    this.elements.dialog = new Couchover.Element('#couchover-body-dialog');
    this.elements.dialog.setHTML('<div style="background-color:black;color:white;padding:3px 5px;font-size:15px;font-weight:bold;">'+title+'</div><div style="color:#222;padding:3px 5px;">'+text+'</div><div id="couchover-body-dialog-controls"></div>');
}


/********** Control **********/

/**
 *  Create control
 *  
 *  @param string type  submit || ok
 *  @param string text
 *  @param callable function_on_control Function on control when you click
 *  @param string place Place of control
 */
Couchover.Control = function (type, text, function_on_control, place) {
    
        // Default place is in Dialog
    place = place ? place : '#couchover-body-dialog-controls';
    
    this.control = document.createElement('input');
    
    var support_type = new Array('submit','ok');
    
    if (support_type.indexOf(type) == -1) {
        alert('UnSupported');
    }
    
    if (type == 'submit') {
        this.control.value = text;
        this.control.type = 'button';
        this.control.onclick = function_on_control;
        this.control.className = 'couchover-control-submit';
    } else if (type == 'ok') {
        this.control.value = text;
        this.control.type = 'button';
        this.control.onclick = function_on_control;
        this.control.className = 'couchover-control-ok';
    }
    
    var controls = new Couchover.Element(place).getElement();
    
    if (controls) {
        controls.appendChild(this.control);
        return true;
    } 
    
    return false;
}


/********** AJAX **********/

/**
 *  AJAX - Communication with server
 *  
 *  @param object setting Configuration
 */
Couchover.AJAX = function (settings) {
    var method = (settings.method == 'POST' || settings.method == 'GET') ? settings.method : 'GET';
    var data = settings.data;
    var target = settings.target;
    var url = settings.url;
    
    var data_string = '';
    var i = 0;
    
    for (var key in data) {
        if (i != 0) {
            data_string += '&';
        } 
        
        data_string += encodeURIComponent(key)+'='+encodeURIComponent(data[key]);
        
        i++;
    }
    
    if (method == 'GET') {
        url += '?'+data_string;
        data_string = null;
    }
    
    var object = false;
    if (window.XMLHttpRequest) {object = new XMLHttpRequest();}
    else if (window.ActiveXObject) {
        try {
            object = new ActiveXObject("Msxml2.XMLHTTP");
        } 
        catch (error) {
            object = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    if (object){
        object.open(method, url, true);
        if (method == 'POST') {
            object.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        }
        object.onreadystatechange = function() {
            if(object.readyState==4 && object.status==200){
                
                if (target) {
                    new Couchover.Element(target).setHTML(object.responseText);
                } else {
                    var source = JSON.parse(object.responseText);
                    if (source) {
                        for (var key in source) {
                            new Couchover.Element(key).setHTML(source[key]);
                        }
                    }
                }
                
            }
        }
        object.send(data_string);
    } 
    
}

$ = Couchover;