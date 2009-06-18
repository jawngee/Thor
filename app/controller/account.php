<?
uses('system.data.model');

class AccountController extends Controller
{
	public function index($id)
	{
		return array('account'=>Model::Instance('provider.account',$id));
	}

	public function create()
	{
	}

	public function edit($id)
	{
		return array('account'=>Model::Instance('provider.account',$id));
	}
}