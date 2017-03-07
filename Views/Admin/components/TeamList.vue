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
</style>

<template>
    <section class="list-team">
        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Équipes</li>
            </ol>
            <button data-toggle="modal" data-target="#createTeamRoleModal"
                    class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Ajouter un rôle
            </button>
        </div>
        <div class="section-body">

            <div class="alert alert-info" role="alert">
                <strong><i class="fa fa-info-circle"></i> Sur cette page vous avez la possibilité de :</strong><br/>
                <p>- de créer, modifier ou supprimer un article</p>
                <p>- de publier ou dépublier un article</p>
                <p>- de créer, modifier ou supprimer une catégorie</p>
            </div>

        </div><!--end .section-body -->

        <div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="deletePostModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cet article ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="createPostCategoryModal" tabindex="-1" role="dialog"
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
        <div class="modal fade" id="editPostCategoryModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="editFormModalLabel">Modifier la catégorie</h4>
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
                                Supprimer la catégorie
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

    import {mapGetters, mapActions} from 'vuex'

    export default
    {
        components: {},
        data () {
            return {
                website_id: this.$route.params.website_id
            }
        },
        computed: {
            ...mapGetters(['auth'])
        },
        methods: {
            ...mapActions([
                'create', 'read', 'update', 'destroy', 'refresh', 'setParams', 'deleteResources'
            ])
        },
        mounted () {

        }
    }
</script>
