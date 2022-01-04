<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"paciente"})
     */
    private $idPaciente;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=false)
     * @Groups({"paciente"})
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
     * @Groups({"paciente"})
     */
    private $tipoDocumento;

    // /**
    //  * @ORM\Column(type="integer")
    //  */
    // private $numSeguroSocial;

    /**
     * @ORM\OneToOne(targetEntity=ContactoEmergencia::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn()
     */
    private $personaContacto;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"paciente"})
     */
    private $numSeguroSocial;

    public function __construct($estado, $numSeguroSocial, $tipoDocumento, $personaContacto){
        $this->estado = $estado;
        $this->tipoDocumento = $tipoDocumento;
        $this->personaContacto = $personaContacto;
        $this->numSeguroSocial = $numSeguroSocial;
    }

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

    public function getNumSeguroSocial(): ?int
    {
        return $this->numSeguroSocial;
    }

    public function setNumSeguroSocial(int $numSeguroSocial): self
    {
        $this->numSeguroSocial = $numSeguroSocial;

        return $this;
    }

    public function getPersonaContacto(): ?ContactoEmergencia
    {
        return $this->personaContacto;
    }

    public function setPersonaContacto(ContactoEmergencia $personaContacto): void
    {
        $this->personaContacto = $personaContacto;
    }


}
