<style>
    .list-team .breadcrumb {
        display: inline-block;
    }
    .list-team button {
        margin-left: 10px;
    }
    .list-team .section-header .btn{
        margin-left: 10px;
    }
    .list-team .list-accordion .tile-text{
        display: inline-block;
        width: 95%;
    }
    .list-team .list-accordion .tile-content{
        display: inline-block;
        padding: 16px;
        vertical-align: top;
        width: 5%;
    }
</style>

<template>
    <section class="list-team">
        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Équipe</li>
            </ol>
            <button data-toggle="modal"
                    class="btn ink-reaction btn-raised btn-lg btn-primary pull-right">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                Enregistrer
            </button>
        </div>
        <div class="section-body">

            <div class="alert alert-info" role="alert">
                <strong><i class="fa fa-info-circle"></i> Sur cette page vous avez la possibilité de :</strong><br/>
                <p>- de créer, modifier ou supprimer un article</p>
                <p>- de publier ou dépublier un article</p>
                <p>- de créer, modifier ou supprimer une catégorie</p>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-head style-primary">
                        <header><i class="fa fa-fw fa-tag"></i> Rôles</header>
                    </div>
                    <div class="card-body">
                        <ul class="list">
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-text">Inbox</div>
                                </a>
                                <a class="btn btn-flat ink-reaction">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-text">Starred</div>
                                </a>
                                <a class="btn btn-flat ink-reaction">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </li>
                            <li class="tile">
                                <a class="tile-content ink-reaction">
                                    <div class="tile-text">Sent email</div>
                                </a>
                                <a class="btn btn-flat ink-reaction">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </li>
                        </ul>
                    </div><!--end .card-body -->
                </div><!--end .card -->
                <button data-toggle="modal" data-target="#createTeamRoleModal"
                        class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Ajouter un rôle
                </button>
            </div>

            <div class="col-md-8">
                <div class="card-head style-primary">
                    <header><i class="fa fa-users"></i> Équipe</header>
                </div>
                <div class="card-body no-padding">
                    <!-- BEGIN RESULT LIST -->
                    <div class="list-results list-results-underlined">
                        <ul class="list list-accordion panel-group" id="team-accordion" data-sortable="true">
                            <member v-for="(member, key) in team" :key="key" :index="key" :member="member" :roles="roles" :website_id="website_id"></member>
                        </ul>
                        <button data-toggle="modal" @click="addMember"
                                class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Ajouter un collaborateur
                        </button>
                    </div><!--end .list-results -->
                    <!-- END RESULTS LIST -->
                </div>
            </div><!--end .section-body -->
        </div>

        <div class="modal fade" id="deleteMemberModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="deleteMemberModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce membre de votre équipe ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <div class="modal fade" id="createTeamRoleModal" tabindex="-1" role="dialog"
             aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="createFormModalLabel">Ajouter une catégorie</h4>
                    </div>
                    <form class="form" role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            <button type="button" data-dismiss="modal" class="btn btn-primary">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" id="editTeamRoleModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="editFormModalLabel">Modifier le rôle</h4>
                    </div>
                    <form class="form" role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-danger">
                                Supprimer le rôle
                            </button>
                            <button type="button" data-dismiss="modal" class="btn btn-primary">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </section>

</template>


<script type="text/babel">

    import '@admin/libs/jquery-ui/jquery-ui.min'
    import pagination_mixin from '@front/mixin/pagination'

    import {mapGetters, mapActions} from 'vuex'

    export default
    {
        components: {
            Member: resolve => { require(['./Member.vue'], resolve) },
        },
        mixins: [pagination_mixin],
        data () {
            return {
                website_id: this.$route.params.website_id,
                team: [],
                roles: [],
                selected_items: []
            }
        },
        computed: {
            ...mapGetters(['auth'])
        },
        methods: {
            ...mapActions([
                'create', 'read', 'update', 'destroy', 'refresh', 'setParams', 'deleteResources'
            ]),
            addMember(){
                this.team.push({
                    full_name: 'Membre ' + this.team.length,
                    description: '',
                    gender: 0,
                    order: this.team.length,
                    roles: [],
                    photo: {
                        path: '/public/media/user/default-photo.png',
                        alt: ''
                    }
                });
            }
        },
        created() {

        },
        mounted () {
            let o = this;
            $('[data-sortable="true"]').sortable({
                placeholder: "ui-state-highlight",
                delay: 100,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.outerHeight() - 1);
                },
                stop: function (event, ui) {
                    let new_postions = [];
                    $('#' + ui.item[0].parentNode.id + ' > li').each((index, li) => {
                        let name = $(li).attr('data-name');
                        let i = o.team.findIndex((i) => i.full_name == name);
                        new_postions[i] = index;
                    });
                    new_postions.forEach((element, index) => {
                        o.team[index].order = element;
                    })
                }
            });
        }
    }
</script>
