<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	  private $officerRepo;
    /**
     * @var
     */
    private $moderator;

    function __construct(RepositoryInterface $officerRepo, \Eendonesia\Moderator\RepositoryInterface $moderator)
    {
        $this->officerRepo = $officerRepo;
        $this->moderator = $moderator;

        return parent::__construct();
    }

    public function create(Request $request)
    {
        $officerId = $request->get('officer_id');

        $officer = $this->officerRepo->find($officerId);
        return view('backend.user.create', compact('officer'));
    }

    public function store(UserForm $request)
    {
        $officer = $this->officerRepo->find($request->get('officer_id'));
        with(new UserCreator())->createFromOfficer($request->all(), $officer);

        return redirect()->route('backend.officers.edit', [$officer->id])->with('flash.success', 'Akun baru berhasil dibuat');
    }

    public function destroy($id)
    {
        $officer = $this->officerRepo->find($id);

        with(new UserCreator())->deleteFromOfficer($officer);
        return redirect()->route('backend.officers.edit', [$id])->with('flash.success', 'Akun baru berhasil dihapus');
    }

    public function resetPassword(Request $form)
    {
        $officer = $this->officerRepo->find($form->get('id'));
        $password = $this->moderator->resetOfficerPassword($officer);

        return json_encode(['status' => 1, 'password' => $password]);
    }
}
