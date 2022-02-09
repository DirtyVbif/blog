class SummaryControl
{
    constructor()
    {
        // class default settings
        this.s = {
            summary_id: 'summary',
            btn_print_id: 'button-summary-print',
            btn_open_id: 'button-summary-open'
        }

        // class parameters
        this.summary;
        this.btn_print;
        this.btn_open;
        this.summary_collapsed_height;
        this.summary_full_height;
        this.summary_clone;
        this.summary_parent;
        this.expanded = false;
    }

    initialize()
    {
        this.summary = document.getElementById(this.s.summary_id);
        if (!this.summary) {
            return;
        }
        this._getSummaryHeight();
        window.addEventListener('resize', event => {
            if (!this.expanded) {
                this._getSummaryHeight();
            }
        });
        this._initButtonOpen();
        this._initButtonPrint();
    }

    _getSummaryHeight()
    {
        if (!this.summary_clone) {
            this.summary_clone = this.summary.cloneNode(true);
            this.summary_clone.removeAttribute('id');
            this.summary_clone.style.height = 'auto';
            this.summary_clone.style.width = '100%';
            this.summary_clone.style.position = 'absolute';
            this.summary_clone.style.zIndex = -999;
            this.summary_clone.style.visibility = 'hidden';
            this.summary_parent = this.summary.parentElement;
        }
        this.summary_collapsed_height = parseFloat(this.summary.offsetHeight);
        this.summary_parent.appendChild(this.summary_clone);
        this.summary_full_height = parseFloat(this.summary_clone.offsetHeight);
        this.summary_clone.remove();
    }

    _initButtonOpen()
    {
        this.btn_open = document.getElementById(this.s.btn_open_id);        
        if (!this.btn_open) {
            return;
        }
        this.btn_open.addEventListener('click', event => {
            this._open(event)
        });
    }

    _open(event)
    {
        this.btn_open.removeEventListener('click', event);
        this.btn_open.style.display = 'none';
        this.expanded = true;
        this.summary.animate([
            { height: this.summary_collapsed_height + 'px' },
            { height: this.summary_full_height + 'px' }
        ], 1000).onfinish = () => {
            this.summary.style.height = 'auto';
        };
    }

    _initButtonPrint()
    {
        this.btn_print = document.getElementById(this.s.btn_print_id);
        if (!this.btn_print) {
            return;
        }
        this.btn_print.addEventListener('click', event => {
            this._print(event);
        });
    }

    _print(event)
    {
        const print_window = window.open('', '', 'height=600, width=600');    
        print_window.document.write('<html>');
        print_window.document.write('<head><style type="text/css" media="all">.summary-card{min-height:150px;}.summary-card img{float:left;margin-right:15px;width:150px;height:auto;max-width:40%;object-fit:contain;border-radius:4px;}</style></head>');
        print_window.document.write('<body>');
        print_window.document.write(this.summary.innerHTML);
        print_window.document.write('</body></html>');
        print_window.document.getElementById(this.s.btn_print_id).remove();
        print_window.document.close();
        print_window.print();
    }
}