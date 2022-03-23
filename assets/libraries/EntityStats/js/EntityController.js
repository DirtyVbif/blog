class EntityController
{
    get s() {
        return {
            /** @type {string} */
            trigger: '.js-entity'
        };
    }

    constructor()
    {
        /**
         * @type {EntityStatsEntity[]}
         */
        this.entities = [];
    }

    init()
    {
        let entities = document.querySelectorAll(this.s.trigger);
        if (entities.length < 1) {
            return;
        }
        for (let i = 0; i < entities.length; i++) {
            const entity = new EntityStatsElement(entities[i]);
            entity.init();
            entity.onload();
            this.entities.push(entity);
        }
    }
}