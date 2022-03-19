class ItemProjector
{
    constructor()
    {
        this.s = {
            trigger_list: '.js-item-projector__list',
            trigger_item: '.js-item-projector__item',
            attr_data_prefix: 'data-item-project-',
            id_prefix: 'item-project--',
            projector_class: 'projected',
            options: {
                item: [
                    {
                        name: "remove-class",
                        method: '_itemRemoveClass'
                    }
                ]
            }
        };

        /**
         * @type {ItemProjectorList[]}
         */
        this.lists = [];
    }

    init()
    {
        let lists = document.querySelectorAll(this.s.trigger_list);
        if (lists.length < 1) {
            return;
        }
        for (let list of lists) {
            let items = list.querySelectorAll(this.s.trigger_item),
                placeholder_id = list.getAttribute('data-item-project-placeholder'),
                placeholder = document.getElementById(placeholder_id);
            if (items.length < 1 || !placeholder) {
                continue;
            }
            this.lists.push(new ItemProjectorList(list, items, placeholder));
        }
        if (this.lists.length < 1) {
            return;
        }
        this._initializeItemClickEvent();
        this._startProjectingRandomItems();
    }

    _initializeItemClickEvent()
    {
        for (let i = 0; i < this.lists.length; i++) {
            for (let x = 0; x < this.lists[i].items.length; x++) {
                this.lists[i].items[x].addEventListener('click', () => {
                    this._projectItem(this.lists[i], x);
                });
            }
        }
    }

    /**
     * @param {ItemProjectorList} list
     * 
     * @param {int} index index of currently projected DOM Element in list
     */
    _projectItem(list, index)
    {
        /** @type {HTMLElement} iClone */
        let iClone = list.items[index].cloneNode(true);
        for (let x = 0; x < this.s.options.item.length; x++) { 
            let attribute = this.s.options.item[x];           
            for (let i = 0; i < iClone.children.length; i++) {
                this._preproccessItemCloneDataAttribute(iClone.children[i], attribute);
                this._preproccessItemCloneId(iClone.children[i]);
            }
            this._preproccessItemCloneDataAttribute(iClone, attribute);
            this._preproccessItemCloneId(iClone);
        }
        this._setOldProjectorClass(list);
        if (!list.items[index].classList.contains(this.s.projector_class)) {
            list.items[index].classList.add(this.s.projector_class);
        }
        list.projectNewItem(iClone, index);
    }

    /**
     * @param {HTMLElement} item 
     * @param {{name:{string}, method:{string}}} attribute 
     */
    _preproccessItemCloneDataAttribute(item, attribute)
    {
        let attribute_name = this.s.attr_data_prefix + attribute.name,
            value = item.getAttribute(attribute_name);
        if (!value) {
            return;
        }
        item.removeAttribute(attribute_name);
        this[attribute.method](item, value);
    }

    /**
     * @param {HTMLElement} item 
     */
    _preproccessItemCloneId(item)
    {
        if (!item.id) {
            return;
        }
        item.id = this.s.id_prefix + item.id;
    }

    /**
     * @param {ItemProjectorList} list
     */
     _setOldProjectorClass(list)
    {
        if (list.projected_item < 0) {
            return;
        } else if (list.items[list.projected_item].classList.contains(this.s.projector_class)) {
            list.items[list.projected_item].classList.remove(this.s.projector_class);
        }
    }

    /**
     * @param {HTMLElement} item that contains specified class to remove
     * @param {string} classname name of class to remove
     */
    _itemRemoveClass(item, classname)
    {
        classname = classname.replace(' ', '').split(',');
        for (let i = 0; i < classname.length; i++) {
            if (item.classList.contains(classname[i])) {
                item.classList.remove(classname[i]);
            }
        }
        return;
    }

    _startProjectingRandomItems()
    {
        for (let i = 0; i < this.lists.length; i++) {
            this._projectRandomListItem(this.lists[i]);
            this.lists[i].setRandomProjectionTimer(() => {
                this._projectRandomListItem(this.lists[i]);
            });
        }
    }
    
    /**
     * @param {ItemProjectorList} list 
     */
    _projectRandomListItem(list)
    {
        let index = list.newRandomItemIndex();
        this._projectItem(list, index);
    }
}