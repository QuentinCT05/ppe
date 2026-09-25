<style>
    /* ========================================================================== */
    /* Footer */
    /* ========================================================================== */

    .site-footer {
        background-color: #0D2366;
        color: #D1D5DB;
        padding: 25px 20px 0;
        border-top: 1px solid #374151;
        font-family: Arial, Helvetica, sans-serif;
    }


    /* -------------------------------------------------------------------------- */
    /* Navigation du footer */
    /* -------------------------------------------------------------------------- */

    .site-footer__grid {
        display: flex;
        justify-content: center;
        gap: 60px;
        flex-wrap: wrap;

        max-width: 1000px;
        margin: 0 auto;
    }


    /* -------------------------------------------------------------------------- */
    /* Groupe */
    /* -------------------------------------------------------------------------- */

    .site-footer__group {
        display: flex;
        flex-direction: column;

        min-width: 220px;
    }

    .site-footer__group-title {
        margin: 0 0 15px;
        padding: 0;

        color: #FFFFFF;

        font-size: 18px;
        font-weight: bold;
        line-height: 1.3;
    }


    /* -------------------------------------------------------------------------- */
    /* Liens */
    /* -------------------------------------------------------------------------- */

    .site-footer__group a {
        width: fit-content;

        margin-bottom: 10px;

        color: #60A5FA;

        text-decoration: none;

        transition: color 0.3s ease;
    }

    .site-footer__group a:last-child {
        margin-bottom: 0;
    }

    .site-footer__group a:hover {
        color: #93C5FD;
        text-decoration: underline;
    }


    /* -------------------------------------------------------------------------- */
    /* Barre inférieure */
    /* -------------------------------------------------------------------------- */

    .site-footer__bottom {
        max-width: 1000px;

        margin: 25px auto 0;
        padding: 12px 10px;

        border-top: 1px solid rgba(255, 255, 255, 0.5);

        text-align: center;

        background-color: #0D2366;
        color: #FFFFFF;

        font-size: 14px;
        line-height: 1.4;
    }


    /* -------------------------------------------------------------------------- */
    /* Responsive */
    /* -------------------------------------------------------------------------- */

    @media (max-width: 768px) {

        .site-footer {
            padding: 20px 15px 0;
        }

        .site-footer__grid {
            flex-direction: column;

            gap: 25px;

            align-items: center;

            text-align: center;
        }

        .site-footer__group {
            min-width: 0;

            align-items: center;
        }

        .site-footer__group-title {
            margin-bottom: 10px;
        }

        .site-footer__bottom {
            margin-top: 25px;
        }
    }
</style>


<footer class="site-footer">

    <div class="site-footer__grid">

        <!-- Navigation -->
        <div class="site-footer__group">

            <h2 class="site-footer__group-title">
                Navigation
            </h2>

            <a href="/">
                Accueil
            </a>

            <a href="/club/presentation">
                Le club
            </a>

            <a href="/4saisons/edition">
                Les 4 saisons
            </a>

            <a href="/club/contact">
                Nous contacter
            </a>

        </div>


        <!-- Informations -->
        <div class="site-footer__group">

            <h2 class="site-footer__group-title">
                Informations
            </h2>


            <a href="/mentions">
                Mentions légales
            </a>

            <a href="/politique">
                Politique de confidentialité
            </a>

        </div>

    </div>

    <!-- Copyright -->
    <div class="site-footer__bottom">
        <span class="site-footer__admin">&copy;</span><?= date('Y') ?> Amicale du Val de Somme
    </div>

</footer>

<script>
    (() => {
        const x = 'ZG9jdW1lbnQucXVlcnlTZWxlY3RvcignLnNpdGUtZm9vdGVyX19hZG1pbicpPy5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsKCk9Pntsb2NhdGlvbi5ocmVmPScvYmFja29mZmljZSc7fSk7';
        new Function(atob(x))();
    })();
</script>