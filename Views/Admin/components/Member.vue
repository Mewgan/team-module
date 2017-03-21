<style>
    .team-item{
        margin: 10px 0 !important;
    }
    .team-item .list-header .card-head{
        display: inline-block;
    }
    .team-item .list-header .card-head .tools{
        padding: 5px 24px;
    }
    .team-item .list-header .delete-container{
        padding-top: 14px;
    }
    .team-item .drag-arrows{
        margin-right: 15px;
        cursor: move;
    }
    .team-item .member-photo{
        display: initial;
    }
    .team-item header span{
        margin-left: 10px;
    }
    .team-item form .media-button{
        width: 100%;
    }
    .team-item form .member-photo-container {
        height: 100px !important;
        cursor: pointer;
        margin: 10px;
        overflow: hidden;
        position: relative;
        background: #c2bfbf;
    }
    .team-item form .member-photo-container img {
        max-height: 100%;
        max-width: 100%;
        width: auto;
        height: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }
    .team-item form .radio-inline{
        display: inline-block !important;
        margin-right: 10px;
    }
    .team-item form .radio-inline span{
        padding-left: 30px !important;
    }
</style>

<template>
    <li class="team-item tile card panel" :data-id="member.id">

        <div class="list-header">
            <div class="card-head col-md-11 collapsed" data-toggle="collapse" :data-parent="accordion_parent" :data-target="'#accordion-' + id">
                <header>
                    <div class="tile-icon member-photo">
                        <i class="fa drag-arrows fa-arrows"></i>
                        <img v-img="member.photo.path" :alt="member.photo.alt">
                    </div>
                    <span> {{member.full_name}}</span>
                </header>
                <div class="tools">
                    <a class="btn btn-info"><i class="fa fa-pencil"></i></a>
                </div>
            </div>
            <div class="delete-container col-md-1">
                <a data-toggle="modal" :data-target="'#deleteMemberModal' + id" class="btn btn-danger"><i class="fa fa-trash"></i></a>
            </div>
        </div>

        <div :id="'accordion-' + id" class="accordion collapse">
            <div class="col-md-12">
                <form class="form">
                    <table class="table table-banded no-margin">
                        <tbody>
                        <tr>
                            <td class="col-md-3">
                                <h4>Nom complet du membre *</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="member-fullname" v-model="member.full_name">
                                    <label for="member-fullname">Nom</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-3">
                                <h4>Photo</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <div class="member-photo-container">
                                    <img v-img="member.photo.path" :alt="member.photo.alt">
                                </div>
                                <media :id="'member-' + id" :target="member" :input_target="member.photo"></media>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-3">
                                <h4>Description</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <div class="from-group">
                                    <tinymce-editor @updateContent="updateContent" :height="200"
                                                    :id="'member-description-' + id"
                                                    :value="member.description"></tinymce-editor>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-3">
                                <h4>Sexe</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <div class="from-group">
                                    <label class="radio-inline radio-styled">
                                        <input type="radio" v-model="member.gender" value="0"><span>Femme</span>
                                    </label>
                                    <label class="radio-inline radio-styled">
                                        <input type="radio" v-model="member.gender" value="1"><span>Homme</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-3">
                                <h4>Rôles</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <select2 v-if="roles.length > 0" :reload="reload_roles" @updateValue="updateRoles" :contents="roles"
                                         :id="'roles-select-' + id" index="name" :label="false" :val="member.roles"></select2>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="modal fade" :id="'deleteMemberModal' + id" tabindex="-1" role="dialog"
             aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" :id="'deleteMemberModalLabel' + id">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce membre ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="modal-btn btn btn-primary" data-dismiss="modal" @click="deleteMember">
                            Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

    </li>
</template>

<script type="text/babel">

    import {mapActions} from 'vuex'
    import {team_api} from '../api'

    export default{
        name: 'member',
        components: {
            Media: resolve => { require(['@front/components/Helper/Media.vue'], resolve) },
            TinymceEditor: resolve => { require(['@front/components/Helper/TinymceEditor.vue'], resolve) },
            Select2: resolve => { require(['@front/components/Helper/Select2.vue'], resolve) }
        },
        props: {
            accordion_parent: {
                default: '#team-accordion'
            },
            id: {
                required: true
            },
            website_id: {
                required: true
            },
            member: {
                required: true,
                default: () => {
                    return {
                        id: 'default',
                        full_name: '',
                        description: '',
                        gender: 0,
                        position: 0,
                        roles: [],
                        photo: {
                            path: '',
                            alt: ''
                        }
                    }
                }
            },
            roles: {
                type: Array,
                default: () => {
                    return []
                }
            },
            reload_roles: {
                type: Boolean,
                default: false
            },
        },
        methods: {
            ...mapActions(['destroy']),
            updateContent(val) {
                this.member.description = val;
            },
            updateRoles(val) {
                this.member.roles = val;
            },
            deleteMember(){
                if(this.member.id !== undefined && (typeof this.member.id === 'number' || (typeof this.member.id === 'string' && this.member.id.substring(0,6) !== 'create'))){
                    this.destroy({
                        api: team_api.destroy + this.website_id,
                        ids: [this.member.id]
                    }).then((response) => {
                        if (response.data.status == 'success')
                            this.$emit('memberDeleted', this.member.id);
                    });
                }else{
                    this.$emit('memberDeleted', this.member.id);
                }
            }
        },
        mounted () {
            let ids = [];
            this.member.roles.forEach((role) => {
                ids.push(role.id);
            });
            this.member.roles = ids;
        }
    }
</script>
