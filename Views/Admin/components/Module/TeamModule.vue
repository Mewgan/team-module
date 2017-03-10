<style>
    .module-title {
        padding: 10px;
        background: #f2f2f2;
    }
</style>

<template>
    <div class="edit-team-module">
        <form class="form">
            <div>
                <h5 class="module-title">Information :</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="content.name" :id="'content-name-' + line">
                            <label :for="'content-name-' + line">Nom *</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="content.block"
                                   :id="'content-block-' + line">
                            <label :for="'content-block-' + line">Bloc *</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.category.title" readonly
                                   :id="'content-module-' + line">
                            <label :for="'content-module-' + line">Module</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.name" readonly
                                   :id="'content-extension-' + line">
                            <label :for="'content-extension-' + line">Extension</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" v-model="content_data.class" :id="'content-class-' + line">
                    <label :for="'content-class-' + line">Class</label>
                </div>
            </div>
            <h5 class="module-title">Choix du template :</h5>
            <template-editor :id="line" :templates="templates" :template="content.template"
                             label="Template du contenu"></template-editor>
            <h5 class="module-title">Configuration avancé :</h5>
            <select2 @updateValue="updateRoles"
                     :contents="roles" :id="'roles-' + line" val_index="id" index="name"
                     label="Choisir les rôles à afficher ou laisser vide pour tout afficher"
                     :val="content_data.roles"></select2>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button type="button" @click="updateContent" class="btn btn-primary" data-dismiss="modal">Enregistrer
            </button>
        </div>

    </div>
</template>

<script type="text/babel">

    import {mapActions} from 'vuex'
    import {template_api} from '@front/api'
    import {team_role_api} from '../../api'

    import module_mixin from '@front/mixin/module'

    export default{
        name: 'price',
        components: {
            TemplateEditor: resolve => {
                require(['@front/components/Helper/TemplateEditor.vue'], resolve)
            },
            Select2: resolve => {
                require(['@front/components/Helper/Select2.vue'], resolve)
            }
        },
        mixins: [module_mixin],
        props: {
            line: {
                default: 'default'
            },
            content: {
                type: Object,
                required: true
            },
            page: {
                default: null
            },
            website: {
                required: true
            }
        },
        data(){
            return {
                website_id: this.$route.params.website_id,
                templates: [],
                roles: [],
                content_data: {
                    class: '',
                    roles: []
                }
            }
        },
        watch: {
            'content_data': {
                handler(){
                    this.$set(this.content, 'data', this.content_data);
                },
                deep: true
            }
        },
        methods: {
            ...mapActions(['read', 'setResponse']),
            updateRoles(val){
                this.content.data.roles = val;
            }
        },
        created () {
            this.read({api: template_api.get_website_content_layouts + this.website}).then((response) => {
                this.templates = response.data;
            });
            this.read({api: team_role_api.all + this.website}).then((response) => {
                if(response.data.resource !== undefined)
                    this.roles = response.data.resource;
            });
        },
        mounted(){
            this.$nextTick(function () {
                let o = this;
                if (this.content.data.roles !== undefined && this.content.data.roles instanceof Array) this.content_data = this.content.data;

            })
        }
    }
</script>