<div class="currency-ticker">
    <button class="hamburger" id="hamburger-currency" aria-label="Mostrar moedas" onclick="alternarMenuMoedas()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="ticker-content" id="ticker-content">
        
        <div class="ticker-items">
              <!-- Componente de Previsão do Tempo -->
         <div>
        <?php echo renderizarPrevisaoTempo('São Paulo', 'BR'); ?>
        </div>
            <a class="ticker-item" href="https://www.google.com/search?q=usd&rlz=1C1GCEU_pt-BRBR1121BR1121&oq=usd&gs_lcrp=EgZjaHJvbWUyDwgAEEUYORiDARixAxiABDIMCAEQIxgnGIAEGIoFMgcIAhAAGIAEMgoIAxAAGLEDGIAEMg0IBBAAGIMBGLEDGIAEMgoIBRAAGLEDGIAEMg0IBhAAGIMBGLEDGIAEMg0IBxAAGIMBGLEDGIAEMgcICBAAGI8CMgcICRAAGI8C0gEHNzkxajBqN6gCCLACAfEFb8NRvOVfxCk&sourceid=chrome&ie=UTF-8" target="_blank">
                <i class="fas fa-dollar-sign"></i>
                <span class="currency-name">USD</span>
                <span class="currency-value" id="usd-value">--</span>
            </a>
            <a class="ticker-item" href="https://www.google.com/search?q=eur&sca_esv=c450a65bf0db98a8&rlz=1C1GCEU_pt-BRBR1121BR1121&sxsrf=AE3TifOpbsnCAM-u8KCq8k-QKraOCwiLVA%3A1751590552048&ei=mCZnaLjcApTb5OUPsuSbqAE&ved=0ahUKEwi4iJal_6GOAxWULbkGHTLyBhUQ4dUDCBA&uact=5&oq=eur&gs_lp=Egxnd3Mtd2l6LXNlcnAiA2V1cjIKEAAYgAQYQxiKBTIQEAAYgAQYsQMYQxiDARiKBTIIEAAYgAQYsQMyCBAAGIAEGLEDMgoQABiABBhDGIoFMgoQABiABBhDGIoFMgoQABiABBhDGIoFMgoQABiABBhDGIoFMgoQABiABBhDGIoFMgoQABiABBhDGIoFSKTGA1CivgNYj8QDcAN4AZABAJgBhgGgAYYDqgEDMC4zuAEDyAEA-AEBmAIGoAKqA6gCFMICBxAjGLADGCfCAgoQABiwAxjWBBhHwgINEAAYgAQYsAMYQxiKBcICBxAjGCcY6gLCAg0QIxjwBRgnGMkCGOoCwgITEAAYgAQYQxi0AhiKBRjqAtgBAcICFRAAGIAEGEMYtAIYigUY6gIYCtgBAcICBBAjGCfCAhIQIxjwBRiABBgTGCcYyQIYigXCAgoQIxiABBgnGIoFwgIQEC4YgAQYxwEYJxiKBRivAcICCxAAGIAEGLEDGIMBwgIFEAAYgASYAwrxBVBZHilQx4TQiAYBkAYKugYGCAEQARgBkgcDMy4zoAenFrIHAzAuM7gHkwPCBwUyLTUuMcgHHg&sclient=gws-wiz-serp" target="_blank">
                <i class="fas fa-euro-sign"></i>
                <span class="currency-name">EUR</span>
                <span class="currency-value" id="eur-value">--</span>
            </a>
            <a class="ticker-item" href="https://www.google.com/search?q=btc&sca_esv=c450a65bf0db98a8&rlz=1C1GCEU_pt-BRBR1121BR1121&sxsrf=AE3TifNwQmxEKKCU2PXeG49708Dvg_Yv6g%3A1751590613845&ei=1SZnaJ2yM9KB5OUPjpfd2Ao&ved=0ahUKEwjd8NHC_6GOAxXSALkGHY5LF6sQ4dUDCBA&uact=5&oq=btc&gs_lp=Egxnd3Mtd2l6LXNlcnAiA2J0YzIKECMYgAQYJxiKBTIKEAAYgAQYFBiHAjIMEAAYgAQYQxiKBRgKMgoQABiABBixAxgKMgoQABiABBhDGIoFMg0QABiABBixAxiDARgKMgoQABiABBhDGIoFMgoQABiABBhDGIoFMgoQABiABBgUGIcCMgUQABiABEikDVCwBFiFCnACeAGQAQCYAYsBoAGKA6oBAzAuM7gBA8gBAPgBAZgCBaACpgOoAhPCAgoQABiwAxjWBBhHwgINEAAYgAQYsAMYQxiKBcICDhAAGLADGOQCGNYE2AEBwgITEC4YgAQYsAMYQxjIAxiKBdgBAcICGRAuGIAEGLADGEMYxwEYyAMYigUYrwHYAQHCAgcQIxgnGOoCwgINECMY8AUYJxjJAhjqAsICExAAGIAEGEMYtAIYigUY6gIYCtgBAcICBBAjGCfCAhIQIxjwBRiABBgTGCcYyQIYigXCAgoQIxiABBgnGIoFwgIQEC4YgAQYxwEYJxiKBRivAcICCxAAGIAEGLEDGIMBwgIFEAAYgASYAwrxBVBZHilQx4TQiAYBkAYKugYGCAEQARgBkgcDMy4zoAenFrIHAzAuM7gHkwPCBwUyLTUuMcgHHg&sclient=gws-wiz-serp" target="_blank">
                <i class="fab fa-bitcoin"></i>
                <span class="currency-name">BTC</span>
                <span class="currency-value" id="btc-value">--</span>
            </a>
            <a class="ticker-item" href="https://www.google.com/search?q=gbp&sca_esv=c450a65bf0db98a8&rlz=1C1GCEU_pt-BRBR1121BR1121&sxsrf=AE3TifOZY4x1BV7_3yELlBPLdQqk2mDBMg%3A1751590633336&ei=6SZnaKSoFMSc5OUP0sOT4Ao&ved=0ahUKEwjkwPfL_6GOAxVEDrkGHdLhBKwQ4dUDCBA&uact=5&oq=gbp&gs_lp=Egxnd3Mtd2l6LXNlcnAiA2dicDIKEAAYgAQYQxiKBTIKEAAYgAQYQxiKBTIQEAAYgAQYsQMYQxiDARiKBTIKEAAYgAQYQxiKBTIKEAAYgAQYQxiKBTIIEAAYgAQYsQMyChAAGIAEGEMYigUyCxAAGIAEGLEDGIMBMgoQABiABBhDGIoFMgsQLhiABBjHARivAUisC1CABVioCnACeAGQAQCYAY8BoAGIAqoBAzAuMrgBA8gBAPgBAZgCBKACoQKoAhTCAgcQIxiwAxgnwgIKEAAYsAMY1gQYR8ICDRAAGIAEGLADGEMYigXCAgcQIxgnGOoCwgINECMY8AUYJxjJAhjqAsICExAAGIAEGEMYtAIYigUY6gIYCtgBAcICBBAjGCfCAhIQIxjwBRiABBgTGCcYyQIYigXCAgoQIxiABBgnGIoFwgIIEC4YgAQYsQPCAhEQLhiABBixAxjRAxiDARjHAcICBRAAGIAEmAMJ8QWfWAlYeGCr24gGAZAGCroGBggBEAEYAZIHAzIuMqAHpBSyBwMwLjK4B5ECwgcFMi0zLjHIBxY&sclient=gws-wiz-serp" target="_blank">
                <i class="fas fa-pound-sign"></i>
                <span class="currency-name">GBP</span>
                <span class="currency-value" id="gbp-value">--</span>
            </a>
        </div>
    </div>
</div>
