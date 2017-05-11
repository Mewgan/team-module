<style>
    .team-module .breadcrumb {
        display: inline-block;
    }
    .team-module button {
        margin-left: 10px;
    }
    .team-module .section-header .btn{
        margin-left: 10px;
    }
</style>

<template>
    <section class="team-module">

        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Équipe <a data-toggle="modal" data-target="#infoTeamModal"><i class="fa fa-info-circle"></i></a></li>
            </ol>
        </div>

        <div class="section-body">

            <div class="col-md-4">
                <team-role-list :website_id="website_id" :roles="roles" @reloadTeam="reloadTeam"></team-role-list>
            </div>

            <div class="col-md-8">
                <team-list :website_id="website_id" :team="team" :roles="roles" :reload_roles="reload_roles"></team-list>
            </div><!--end .section-body -->

        </div>

        <!-- Modal Structure -->
        <div class="modal fade" id="infoTeamModal" tabindex="-1" role="dialog"
             aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xlg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="infoTeamModalLabel">Information</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info clearfix" role="alert">
                            <div class="col-md-4 col-sm-12">
                                <h4 class="m0 mb10">1/ Rôles</h4>
                                <p>Gérer les rôles de votre équipe</p>
                                <p><i class="fa fa-pencil mr10"></i> Pour modifier un rôle</p>
                                <p><i class="fa fa-trash mr10"></i> Pour supprimer un rôle</p>
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <h4 class="m0 mb10">2/ Équipe</h4>
                                <p>Gérer les membres de votre équipe</p>
                                <p><i class="fa drag-arrows fa-arrows mr10"></i> Pour définir l'ordre d'affichage de vos membres</p>
                                <p><i class="fa fa-angle-left mr10"></i> Pour modifier un membre</p>
                                <p><i class="fa fa-trash mr10"></i> Pour supprimer un membre</p>
                                <p><i class="fa fa-save"></i> Enregistrer : Pour mettre à jour les membres</p>
                            </div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

    </section>

</template>


<script type="text/babel">

    import {mapActions} from 'vuex'
    import {team_api, team_role_api} from '../api'

    export default
    {
        components: {
            TeamList: resolve => {
                require(['./TeamList.vue'], resolve)
            },
            TeamRoleList: resolve => {
                require(['./TeamRoleList.vue'], resolve)
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                team: [],
                roles: [],
                reload_roles: false
            }
        },
        methods: {
            ...mapActions(['read']),
            reloadTeam(){
                this.loadTeam().then(() => {
                    this.reload_roles = !this.reload_roles;
                });
            },
            loadTeam(){
                return this.read({api: team_api.all + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined) {
                        this.team = response.data.resource;
                        this.team.forEach((member, index) => {
                            let ids = [];
                            if(member.roles instanceof Object){
                                this.team[index].roles = $.map(member.roles, (value) => {
                                    return [value];
                                })
                            }
                            this.team[index].roles.forEach((role) => {
                                ids.push(role.id);
                            });
                            this.team[index].roles = ids;
                        })
                    }
                })
            },
            loadRoles(){
                this.read({api: team_role_api.all + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined) {
                        this.roles = response.data.resource;
                    }
                });
            }
        },
        created() {
            this.loadRoles();
            this.loadTeam();
        }
    }
</script>
