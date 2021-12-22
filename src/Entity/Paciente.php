<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paciente
 *
 * @ORM\Table(name="paciente", indexes={@ORM\Index(name="IDX_C6CBA95E54DF9189B6B12EC7", columns={"tipo_documento", "documento"})})
 * @ORM\Entity(repositoryClass="App\Repository\PacienteRepository")
 */
class Paciente
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_paciente", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="paciente_id_paciente_seq", allocationSize=1, initialValue=1)
     */
    private $idPaciente;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=false)
     */
    private $estado;

    /**
     * @var \PersonData
     *
     * @ORM\ManyToOne(targetEntity="PersonData")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_documento", referencedColumnName="tipo_documento"),
     *   @ORM\JoinColumn(name="documento", referencedColumnName="documento")
     * })
     */
    private $tipoDocumento;

    public function getIdPaciente(): ?int
    {
        return $this->idPaciente;
    }

    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getTipoDocumento(): ?PersonData
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?PersonData $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }


}
