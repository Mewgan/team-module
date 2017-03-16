<style>
    .team-role-list{
        overflow: auto;
    }
</style>

<template>
    <div class="team-role-list">

        <div class="card">
            <div class="card-head style-primary">
                <header><i class="fa fa-fw fa-tag"></i> Rôles</header>
            </div>
            <div class="card-body">
                <ul class="list">
                    <li v-for="role in roles" class="tile">
                        <a class="tile-content ink-reaction">
                            <div class="tile-text">{{ role.name }}</div>
                        </a>
                        <a @click="selectRole(role)" data-toggle="modal" data-target="#editTeamRoleModal"
                           class="btn btn-flat">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a @click="selectRole(role)" data-toggle="modal" data-target="#deleteTeamRoleModal"
                           class="btn btn-flat ink-reaction">
                            <i class="fa fa-trash"></i>
                        </a>
                    </li>
                </ul>
            </div><!--end .card-body -->
        </div><!--end .card -->

        <button data-toggle="modal" @click="clearRole" data-target="#editTeamRoleModal"
                class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
            <i class="fa fa-plus" aria-hidden="true"></i>
            Ajouter un rôle
        </button>

        <div class="modal fade" id="editTeamRoleModal" tabindex="-1" role="dialog"
             aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="editFormModalLabel">Ajouter/Modifier un rôle</h4>
                    </div>
                    <form class="form" role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="role-name" v-model="role.name">
                                        <label for="role-name">Nom</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            <button type="button" data-dismiss="modal" @click="updateOrCreate" class="btn btn-primary">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" id="deleteTeamRoleModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="deleteFormModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer le rôle ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" @click="deleteRole" data-dismiss="modal" class="btn btn-primary">Oui</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </div>

</template>


<script type="text/babel">

    import {mapActions} from 'vuex'
    import {team_role_api} from '../api'

    export default
    {
        name: 'team-role-list',
        props: {
            website_id: {
                required: true
            },
            roles: {
                type: Array,
                default: () => {
                    return [];
                }
            }
        },
        data () {
            return {
                role: {
                    name: ''
                }
            }
        },
        methods: {
            ...mapActions([
                'create', 'update', 'destroy'
            ]),
            clearRole(){
                this.role =  {
                    name: ''
                };
            },
            selectRole(role){
                this.role = role;
            },
            updateOrCreate(){
                if (this.role.id !== undefined) {
                    this.update({
                        api: team_role_api.update + this.role.id + '/' + this.website_id,
                        value: this.role
                    }).then(() => {
                        this.$emit('reloadTeam');
                    })
                } else {
                    this.create({
                        api: team_role_api.create + this.website_id,
                        value: this.role
                    }).then((response) => {
                        if (response.data.resource !== undefined){
                            this.roles.push(response.data.resource);
                        }
                    })
                }
            },
            deleteRole(){
                if (this.role.id !== undefined) {
                    this.destroy({
                        api: team_role_api.destroy + this.website_id,
                        ids: [this.role.id]
                    }).then((response) => {
                        if (response.data.status == 'success'){
                            let index = this.roles.findIndex((i) => i.id == this.role.id);
                            this.roles.splice(index, 1);
                        }
                    })
                }
            }
        }
    }
</script>
