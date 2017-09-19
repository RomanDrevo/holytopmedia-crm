<?php
namespace App;

trait HasPermissions
{
    /**
     * A user may have multiple permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }


    /**
     * Assign the given permission to the user.
     *
     * @param  string $permission
     * @return mixed
     */

    public function assignPermission($name)
    {
        return $this->permissions()->save(
            Permission::whereName($name)->firstOrFail()
        );
    }

    /**
     * Determine if the user has the given permission.
     *
     * @param  mixed $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('name', $permission);
        }

        if( $permission instanceof Permission){
            return $this->permissions->contains('name', $permission->name);
        }

        return !!$permission->intersect($this->permissions)->count();
    }


}