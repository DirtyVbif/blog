class HtmlTagsAutofill
{
    constructor()
    {
        this.s = {
            /**
             * @param {string} trigger_block selector for html tags autofill list
             */
            trigger_block: '.js-html-tags-autofill',

            /**
             * @param {string} trigger_item selector for html tags autofill list items
             */
            trigger_item: '.js-html-tags-autofill__btn'
        };

        /**
         * @param {object[]} blocks array with html tags autofill list elements
         */
        this.blocks = [];
    }

    init()
    {
        let blocks = document.querySelectorAll(this.s.trigger_block);
        if (blocks.length < 1) {
            return;
        }
        for (const block of blocks) {
            let target_id = block.getAttribute('data-target-id'),
                target = document.querySelector('#' + target_id);
            if (!target) {
                continue;
            }
            this.blocks.push({
                /** @param {Element} html html tags autofill list dom element */
                html: block,
                /** @param {Element} target html tags autofill list target textarea dom element */
                target: target
            });
        }
        if (this.blocks.length < 1) {
            return;
        }
        this._initializeEvents();
    }

    _initializeEvents()
    {
        for (let i = 0; i < this.blocks.length; i++) {
            this.blocks[i].items = this.blocks[i].html.querySelectorAll(this.s.trigger_item);
            for (let x = 0; x < this.blocks[i].items.length; x++) {
                this.blocks[i].items[x].addEventListener('click', (event) => {
                    event.preventDefault();
                    this._tagClicked(this.blocks[i], x);
                });
            }
        }
    }

    /**
     * @param {{html:Element, target:Element, items:Element[]}} block 
     * @param {int} i item index in array block.items
     */
    _tagClicked(block, i)
    {
        let tag_start = '<' + block.items[i].innerText + '>',
            tag_end = '</' + block.items[i].innerText + '>',
            pos_start = block.target.selectionStart,
            pos_end = block.target.selectionEnd,
            /** @type {string} input_text */
            input_text = block.target.value;
            
        let txt_before = input_text.substring(0,  pos_start),
            txt_after  = input_text.substring(pos_end, input_text.length),
            txt_middle = '';
        if (pos_start != pos_end) {
            txt_middle  = input_text.substring(pos_start, pos_end);
        }
        input_text = txt_before + tag_start + txt_middle + tag_end + txt_after;
        block.target.value = input_text;
    }
}