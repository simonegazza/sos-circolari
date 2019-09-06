<?php
defined('_JEXEC') or die('Restricted Access');
?>

<style>
    .circolare {
        border: darkgray 1px solid;
        border-radius: 3px;
        width: 35%;
        margin: auto;
        padding: 2%;
    }

    .numero { grid-area: numero; }

    .protocollo { grid-area: protocollo; }

    .oggetto { grid-area: oggetto; }

    .autore { grid-area: autore; }

    .luogo { grid-area: luogo; }

    .data { grid-area: data; }


    .heading {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: 1fr 1fr 1fr;
        grid-template-areas: "numero numero protocollo" "oggetto oggetto luogo" "autore autore data";
    }

    p {
        font-weight: bold;
    }

    .align-right {
        text-align: right;
    }

    input {
        width: 93%;
        background-color: white !important;
    }

    textarea {
        width: 99%;
        height: 50vh;
        resize: none;
        background-color: white !important;
    }

    .numero { grid-area: numero; }

    .protocollo { grid-area: protocollo; }

    .luogo { grid-area: luogo; }

    .data { grid-area: data; }

    .oggetto { grid-area: oggetto; }

    .autore { grid-area: autore; }
</style>

<div class = "circolare">
    <div class="heading">
        <div class="numero">
            <p>Numero</p>
            <input type="text" value="<?php echo $this->circolare->numero; ?>" readonly>
        </div>
        <div class="protocollo align-right">
            <p>Protocollo</p>
            <input type="text" class = "align-right" value="<?php echo $this->circolare->protocollo; ?>" readonly>
        </div>
        <div class="luogo align-right">
            <p>Luogo</p>
            <input type="text" class = "align-right" value="<?php echo $this->circolare->luogo; ?>" readonly>
        </div>
        <div class="data align-right">
            <p>Data</p>
            <input type="text" class = "align-right" value="<?php echo $this->circolare->data; ?>" readonly>
        </div>
        <div class="oggetto">
            <p>Oggetto</p>
            <input type="text" value="<?php echo $this->circolare->oggetto; ?>" readonly>
        </div>
        <div class="autore">
            <p>Autore</p>
            <input type="text" value="<?php echo $this->circolare->name; ?>" readonly>
        </div>
    </div>
    <div class = "body">
        <p>Testo circolare</p>
        <textarea readonly><?php echo $this->circolare->testo; ?></textarea>
    </div>
</div>